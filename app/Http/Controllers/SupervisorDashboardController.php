<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use App\Models\IdpKompetensiPengerjaan;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\NilaiPengerjaanIdp;
use App\Services\IdpRekomendasiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\IdpRekomendasi;

class SupervisorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $spvId = $user->id;

        // Ambil tahun dari query atau default ke tahun sekarang
        $tahunDipilih = $request->input('tahun', now()->year);

        // Ambil daftar tahun unik dari waktu_mulai IDP untuk dropdown filter
        $listTahun = IDP::where('id_supervisor', $spvId)
            ->where('is_template', false)
            ->selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->pluck('tahun')
            ->sortDesc();

        $idpIds = IDP::where('id_supervisor', $spvId)
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->pluck('id_idp');

        $jumlahIDPGiven = IDP::where('id_supervisor', $spvId)
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $jumlahRekomendasiBelumMuncul = IDP::where('id_supervisor', $spvId)
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->where(function ($query) {
                $query->doesntHave('rekomendasis')
                    ->orWhereHas('rekomendasis', function ($q) {
                        $q->whereNull('hasil_rekomendasi')->orWhere('hasil_rekomendasi', '');
                    });
            })->count();

        $jumlahDisarankan = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->where('hasil_rekomendasi', 'Disarankan')->count();

        $jumlahDisarankanDenganPengembangan = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->where('hasil_rekomendasi', 'Disarankan dengan Pengembangan')->count();

        $jumlahTidakDisarankan = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->where('hasil_rekomendasi', 'Tidak Disarankan')->count();

        $jumlahMenungguPersetujuan = IDP::where('id_supervisor', $spvId)
            ->where('status_approval_mentor', 'Menunggu Persetujuan')
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $jumlahIDPRevisi = IDP::where('id_supervisor', $spvId)
            ->where('status_pengajuan_idp', 'Revisi')
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $jumlahIdpTidakDisetujui = IDP::where('id_supervisor', $spvId)
            ->where('status_pengajuan_idp', 'Tidak Disetujui')
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $jumlahIdpMenungguPersetujuan = IDP::where('id_supervisor', $spvId)
            ->where('status_pengajuan_idp', 'Menunggu Persetujuan')
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $rekomendasiData = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->with('idp.karyawan.roles')
            ->get()
            ->filter(function ($item) use ($spvId) {
                return $item->idp &&
                    $item->idp->id_supervisor == $spvId &&
                    $item->idp->karyawan &&
                    $item->idp->karyawan->roles->contains('id_role', 4);
            })
            ->map(function ($item) {
                return [
                    'x' => $item->nilai_akhir_hard,
                    'y' => $item->nilai_akhir_soft,
                    'label' => ($item->idp->karyawan->name ?? 'Tidak Diketahui') . ' - ' . ($item->idp->proyeksi_karir ?? '-'),
                ];
            })->values();

        $topKaryawan = IdpRekomendasi::with('idp.karyawan')
            ->where('hasil_rekomendasi', 'Disarankan')
            ->whereIn('id_idp', $idpIds)
            ->orderByDesc('nilai_akhir_soft')
            ->orderByDesc('nilai_akhir_hard')
            ->take(5)
            ->get();

        $jenjangData = IDP::select('id_jenjang', DB::raw('count(*) as total'))
            ->where('id_supervisor', $spvId)
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->groupBy('id_jenjang')
            ->with('jenjang')
            ->get();

        $jenjangLabels = [];
        $jenjangTotals = [];
        foreach ($jenjangData as $data) {
            $jenjangLabels[] = $data->jenjang ? $data->jenjang->nama_jenjang : 'Tidak diketahui';
            $jenjangTotals[] = (int) $data->total;
        }

        $LGData = IDP::select('id_LG', DB::raw('count(*) as total'))
            ->where('id_supervisor', $spvId)
            ->where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->groupBy('id_LG')
            ->with('learningGroup')
            ->get();

        $LGLabels = [];
        $LGTotals = [];
        foreach ($LGData as $data) {
            $LGLabels[] = $data->learningGroup ? $data->learningGroup->nama_LG : 'Tidak diketahui';
            $LGTotals[] = (int) $data->total;
        }

        $totalBelumEvaluasiPasca = IDP::where('id_supervisor', $spvId)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->whereHas('rekomendasis', function ($query) {
                $query->whereIn('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan']);
            })
            ->whereDoesntHave('evaluasiIdp', function ($query) {
                $query->where('jenis_evaluasi', 'pasca')
                    ->where('sebagai_role', 'supervisor');
            })
            ->count();

        return view('supervisor.spv-dashboard', [
            'type_menu' => 'dashboard',
            'tahunDipilih' => $tahunDipilih,
            'listTahun' => $listTahun,
            'jumlahIDPGiven' => $jumlahIDPGiven,
            'jumlahRekomendasiBelumMuncul' => $jumlahRekomendasiBelumMuncul,
            'jumlahDisarankan' => $jumlahDisarankan,
            'jumlahDisarankanDenganPengembangan' => $jumlahDisarankanDenganPengembangan,
            'jumlahTidakDisarankan' => $jumlahTidakDisarankan,
            'jumlahMenungguPersetujuan' => $jumlahMenungguPersetujuan,
            'jumlahIDPRevisi' => $jumlahIDPRevisi,
            'jumlahIdpTidakDisetujui' => $jumlahIdpTidakDisetujui,
            'jumlahIdpMenungguPersetujuan' => $jumlahIdpMenungguPersetujuan,
            'dataPoints' => $rekomendasiData,
            'topKaryawan' => $topKaryawan,
            'jenjangLabels' => $jenjangLabels,
            'jenjangTotals' => $jenjangTotals,
            'LGLabels' => $LGLabels,
            'LGTotals' => $LGTotals,
            'totalBelumEvaluasiPasca' => $totalBelumEvaluasiPasca,
        ]);
    }

    public function indexSupervisor(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $id_LG = $request->query('lg');
        $tahun = $request->query('tahun');
        $listTahun = IDP::whereNotNull('waktu_mulai')
            ->selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');
        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        $idps = IDP::with([
            'mentor',
            'supervisor',
            'karyawan',
            'idpKompetensis.pengerjaans',
        ])
            ->where('id_supervisor', $user->id)
            ->when($search, function ($query, $search) {
                return $query->where('proyeksi_karir', 'like', "%$search%");
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->where('id_jenjang', $id_jenjang);
            })
            ->when($id_LG, function ($query, $id_LG) {
                return $query->where('lg', $id_LG);
            })
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('waktu_mulai', $tahun);
            })
            ->orderByDesc('created_at')
            ->get();

        $filtered = $idps->filter(function ($item) {
            $total = $item->idpKompetensis->count();
            $selesai = 0;

            foreach ($item->idpKompetensis as $kom) {
                $totalUpload = $kom->pengerjaans->count();
                $jumlahDisetujui = $kom->pengerjaans
                    ->where('status_pengerjaan', 'Disetujui Mentor')->count();

                if ($totalUpload > 0 && $totalUpload === $jumlahDisetujui) {
                    $selesai++;
                }
            }

            return $total > 0 && $selesai === $total;
        })->values(); // reset index

        // Manual pagination
        $page = request()->get('page', 1);
        $perPage = 10;
        $paged = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $paged,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('supervisor.IDP.index', [
            'idps' => $paginated,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'supervisor',
        ]);
    }
    public function showSupervisor($id, Request $request)
    {
        $idps = IDP::with([
            'karyawan',
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars',
            'idpKompetensis.pengerjaans'
        ])->findOrFail($id);
        $pengerjaansQuery = IdpKompetensiPengerjaan::whereHas('idpKompetensi', function ($q) use ($id) {
            $q->where('id_idp', $id);
        })->orderBy('created_at', 'desc');

        $pengerjaans = $pengerjaansQuery->paginate(5)->withQueryString();

        // Ambil id_pengerjaan dari query string jika ada
        $highlightPengerjaan = null;
        if ($request->has('pengerjaan')) {
            $highlightPengerjaan = IdpKompetensiPengerjaan::with([
                'idpKompetensi.kompetensi',
            ])->find($request->pengerjaan);
        }
        return view('supervisor.IDP.detail', [
            'idps' => $idps,
            'pengerjaans' => $pengerjaans,
            'highlightPengerjaan' => $highlightPengerjaan,
            'type_menu' => 'karyawan',
        ]);
    }
    private $rekomendasiService;

    public function __construct(IdpRekomendasiService $rekomendasiService)
    {
        $this->rekomendasiService = $rekomendasiService;
    }

    public function store(Request $request)
    {
        try {
            Log::info('=== MULAI PROSES STORE PENILAIAN ===', [
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'rating' => 'required|in:1,2,3,4,5',
                'saran' => 'required|string',
                'id_idpKomPeng' => 'required|exists:idp_kompetensi_pengerjaans,id_idpKomPeng',
            ]);

            $nilai = NilaiPengerjaanIdp::create([
                'id_idpKomPeng' => $validated['id_idpKomPeng'],
                'rating' => $validated['rating'],
                'saran' => $validated['saran'],
            ]);

            // Baru ambil IDP setelah simpan nilai
            $idpKomPeng = $nilai->idpKompetensiPengerjaan;
            $idp = $idpKomPeng->idpKompetensi->idp;

            $idp->refresh();
            // Hitung total dan yang sudah dinilai
            $total = 0;
            $dinilai = 0;

            foreach ($idp->idpKompetensis as $kompetensi) {
                foreach ($kompetensi->pengerjaans as $pengerjaan) {
                    $total++;
                    if ($pengerjaan->nilaiPengerjaanIdp !== null) {
                        $dinilai++;
                    }
                }
            }
            Log::info('📊 Rekap penilaian', [
                'id_idp' => $idp->id_idp,
                'total_pengerjaan' => $total,
                'jumlah_dinilai' => $dinilai,
                'kondisi_terpenuhi' => $total > 0 && $dinilai === $total
            ]);

            // Jalankan rekomendasi jika terpenuhi
            if ($total > 0 && $dinilai === $total) {
                Log::info('🚀 Menjalankan perhitungan rekomendasi', ['id_idp' => $idp->id_idp]);
                $this->rekomendasiService->hitungRekomendasi($idp);
                Log::info('✅ Perhitungan rekomendasi selesai', ['id_idp' => $idp->id_idp]);
            } else {
                Log::info('❌ Belum semua dinilai, rekomendasi belum dihitung', [
                    'sisa' => $total - $dinilai
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Penilaian berhasil disimpan.',
                    'debug_info' => [
                        'total' => $total,
                        'dinilai' => $dinilai,
                        'rekomendasi_dihitung' => $total > 0 && $dinilai === $total
                    ]
                ]);
            }

            return redirect()->back()->with('msg-success', 'Penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('❌ ERROR saat store penilaian', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menyimpan penilaian.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat menyimpan penilaian.');
        }
    }

    public function indexRiwayatIdp(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $id_LG = $request->query('lg');
        $tahun = $request->query('tahun');
        $listTahun = IDP::whereNotNull('waktu_mulai')
            ->selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');
        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        // $listSemester = Semester::all();
        $idps = IDP::query(); // Mulai dengan query dasar
        $idps->where('is_template', false)
            ->where('id_mentor', $user->id) // Ambil IDP hanya milik user login
            ->with(['karyawan', 'karyawan.jenjang', 'karyawan.learningGroup', 'rekomendasis'])
            ->whereHas('rekomendasis', function ($q) {
                $q->whereNotNull('hasil_rekomendasi')
                    ->where('hasil_rekomendasi', '!=', '');
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('mentor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
                });
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->whereHas('karyawan', function ($q) use ($id_jenjang) {
                    $q->where('id_jenjang', $id_jenjang);
                });
            })
            ->when($id_LG, function ($query, $id_LG) {
                return $query->whereHas('karyawan', function ($q) use ($id_LG) {
                    $q->where('lg', $id_LG);
                });
            })
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('waktu_mulai', $tahun);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('supervisor.RiwayatIDP.riwayat', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'supervisor',
        ]);
    }
    public function showRiwayatIdp($id)
    {
        // Mengambil data Divisi berdasarkan ID
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars' // relasi kompetensi beserta metode belajar
        ])->findOrFail($id);
        return view('supervisor.RiwayatIDP.detailRiwayat', [
            'idps'    => $idps,
            'type_menu' => 'supervisor',
        ]);
    }
    public function cetakFiltered(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $query = Idp::with([
            'karyawan',
            'jenjang',
            'jabatan',
            'divisi',
            'penempatan',
            'learninggroup',
            'semester',
            'angkatanpsp',
            'mentor',
            'supervisor',
            'rekomendasis',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars',
            'idpKompetensis.pengerjaans.nilaiPengerjaanIdp'
        ])
            ->where('id_supervisor', $user->id); // Ambil IDP hanya milik user login


        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('karyawan', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('rekomendasis', function ($q2) use ($search) {
                        $q2->where('hasil_rekomendasi', 'like', "%$search%");
                    })
                    ->orWhereHas('learningGroup', function ($q2) use ($search) {
                        $q2->where('nama_LG', 'like', "%$search%");
                    })
                    ->orWhere('proyeksi_karir', 'like', "%$search%");
            });
        }
        // Filter: Jenjang
        if ($request->filled('id_jenjang')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_jenjang', $request->id_jenjang);
            });
        }

        // Filter: Learning Group
        if ($request->filled('id_LG')) {
            $query->whereHas('karyawan.learningGroup', function ($q) use ($request) {
                $q->where('id_LG', $request->id_LG);
            });
        }

        // Filter: Tahun
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Tambahkan filter wajib hasil_rekomendasi ada
        $query->whereHas('rekomendasis', function ($q) {
            $q->whereNotNull('hasil_rekomendasi')->where('hasil_rekomendasi', '!=', '');
        });
        // Ambil hasil query
        $idps = $query->get();

        // Jika hasil kosong, bisa diberi feedback (opsional)
        if ($idps->isEmpty()) {
            return redirect()->back()->with('error', 'Data IDP tidak ditemukan.');
        }

        // Waktu cetak
        $waktuCetak = Carbon::now()->translatedFormat('d F Y H:i');

        // Render PDF
        $pdf = Pdf::loadView('supervisor.RiwayatIDP.riwayat_pdf', [
            'idps' => $idps,
            'type_menu' => 'supervisor',
            'waktuCetak' => $waktuCetak,
        ]);

        return $pdf->stream('Data-IDP.pdf');
    }
}
