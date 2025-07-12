<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiIdp;
use App\Models\User;
use App\Models\IDP;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\IdpRekomendasi;
use App\Models\Panduan;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar tahun dari data IDP
        $listTahun = IDP::selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->pluck('tahun')
            ->sortDesc();

        // Ambil tahun dari request atau default ke tahun sekarang
        $tahunDipilih = $request->input('tahun', now()->year);

        // Filter data berdasarkan tahun
        $jumlahIDPGiven = IDP::where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $jumlahIDPBank = IDP::where('is_template', true)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->count();

        $jumlahRekomendasiBelumMuncul = IDP::where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->where(function ($query) {
                $query->doesntHave('rekomendasis')
                    ->orWhereHas('rekomendasis', function ($q) {
                        $q->whereNull('hasil_rekomendasi')
                            ->orWhere('hasil_rekomendasi', '');
                    });
            })->count();

        $jumlahApplyBankIdp = IDP::where('is_template', false)
            ->whereYear('waktu_mulai', $tahunDipilih)
            ->whereNotNull('id_idp_template_asal')
            ->count();

        $jumlahDisarankan = IdpRekomendasi::whereHas('idp', function ($q) use ($tahunDipilih) {
            $q->whereYear('waktu_mulai', $tahunDipilih);
        })->where('hasil_rekomendasi', 'Disarankan')->count();

        $jumlahDisarankanDenganPengembangan = IdpRekomendasi::whereHas('idp', function ($q) use ($tahunDipilih) {
            $q->whereYear('waktu_mulai', $tahunDipilih);
        })->where('hasil_rekomendasi', 'Disarankan dengan Pengembangan')->count();

        $jumlahTidakDisarankan = IdpRekomendasi::whereHas('idp', function ($q) use ($tahunDipilih) {
            $q->whereYear('waktu_mulai', $tahunDipilih)
                ->where('is_template', false);
        })->where('hasil_rekomendasi', 'Tidak Disarankan')->count();

        $jumlahRekomendasiMenunggu = IdpRekomendasi::whereNull('hasil_rekomendasi')
            ->whereHas('idp', function ($q) use ($tahunDipilih) {
                $q->whereYear('waktu_mulai', $tahunDipilih);
            })->count();

        // Tetap menampilkan data user tanpa filter tahun
        $jumlahKaryawan = User::whereHas('userRoles.role', fn($q) => $q->where('nama_role', 'karyawan'))->count();
        $jumlahSpv = User::whereHas('userRoles.role', fn($q) => $q->where('nama_role', 'supervisor'))->count();
        $jumlahMentor = User::whereHas('userRoles.role', fn($q) => $q->where('nama_role', 'mentor'))->count();

        // Jenjang chart
        $jenjangData = IDP::whereYear('waktu_mulai', $tahunDipilih)
            ->select('id_jenjang', DB::raw('count(*) as total'))
            ->groupBy('id_jenjang')
            ->with('jenjang')
            ->get();

        $jenjangLabels = [];
        $jenjangTotals = [];
        foreach ($jenjangData as $data) {
            $jenjangLabels[] = $data->jenjang ? $data->jenjang->nama_jenjang : 'Tidak diketahui';
            $jenjangTotals[] = (int) $data->total;
        }

        // Learning Group chart
        $LGData = IDP::whereYear('waktu_mulai', $tahunDipilih)
            ->select('id_LG', DB::raw('count(*) as total'))
            ->groupBy('id_LG')
            ->with('learningGroup')
            ->get();

        $LGLabels = [];
        $LGTotals = [];
        foreach ($LGData as $data) {
            $LGLabels[] = $data->learningGroup ? $data->learningGroup->nama_LG : 'Tidak diketahui';
            $LGTotals[] = (int) $data->total;
        }

        $totalPanduan = Panduan::whereYear('created_at', $tahunDipilih)->count();

        $rekomendasiData = IdpRekomendasi::whereHas('idp', function ($q) use ($tahunDipilih) {
            $q->whereYear('waktu_mulai', $tahunDipilih);
        })
            ->with('idp.karyawan.roles')
            ->get()
            ->filter(function ($item) {
                return $item->idp && $item->idp->karyawan && $item->idp->karyawan->roles->contains('id_role', 4);
            })
            ->map(function ($item) {
                return [
                    'x' => $item->nilai_akhir_hard,
                    'y' => $item->nilai_akhir_soft,
                    'label' => ($item->idp->karyawan->name ?? 'Tidak Diketahui') . ' - ' . ($item->idp->proyeksi_karir ?? '-'),
                ];
            })->values();

        $topKaryawan = IdpRekomendasi::with(['idp.karyawan'])
            ->where('hasil_rekomendasi', 'Disarankan')
            ->whereHas('idp', function ($q) use ($tahunDipilih) {
                $q->whereYear('waktu_mulai', $tahunDipilih);
            })
            ->orderByDesc('nilai_akhir_soft')
            ->orderByDesc('nilai_akhir_hard')
            ->take(5)
            ->get();

        $totalEvaluasiPasca = EvaluasiIdp::where('jenis_evaluasi', 'pasca')
            ->whereYear('created_at', $tahunDipilih)
            ->count();
        IdpController::autoGenerateRekomendasi();
        return view('adminsdm.dashboard', [
            'type_menu' => 'dashboard',
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahSpv' => $jumlahSpv,
            'jumlahMentor' => $jumlahMentor,
            'jumlahIDPGiven' => $jumlahIDPGiven,
            'jumlahIDPBank' => $jumlahIDPBank,
            'jumlahDisarankan' => $jumlahDisarankan,
            'jumlahDisarankanDenganPengembangan' => $jumlahDisarankanDenganPengembangan,
            'jumlahTidakDisarankan' => $jumlahTidakDisarankan,
            'jumlahRekomendasiBelumMuncul' => $jumlahRekomendasiBelumMuncul,
            'jumlahRekomendasiMenunggu' => $jumlahRekomendasiMenunggu,
            'jumlahApplyBankIdp' => $jumlahApplyBankIdp,
            'jenjangLabels' => $jenjangLabels,
            'jenjangTotals' => $jenjangTotals,
            'LGLabels' => $LGLabels,
            'LGTotals' => $LGTotals,
            'totalPanduan' => $totalPanduan,
            'dataPoints' => $rekomendasiData,
            'topKaryawan' => $topKaryawan,
            'totalEvaluasiPasca' => $totalEvaluasiPasca,
            'listTahun' => $listTahun,
            'tahunDipilih' => $tahunDipilih,
        ]);
    }

    public function indexRiwayatIdp(Request $request)
    {
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

        return view('adminsdm.BehaviorIDP.RiwayatIDP.riwayat-idp', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'idps',
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
        return view('adminsdm.BehaviorIDP.RiwayatIDP.detail-riwayat', [
            'idps'    => $idps,
            'type_menu' => 'idps',
        ]);
    }
    public function cetakPDF($id)
    {
        $idps = IDP::with([
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
        ])->findOrFail($id);

        $pdf = Pdf::loadView('adminsdm.BehaviorIDP.RiwayatIDP.riwayat_pdf', compact('idps'))->setPaper('a4', 'portrait');

        return $pdf->download('Detail-IDP-' . $idps->karyawan->name . '.pdf');
    }
    public function cetakFiltered(Request $request)
    {
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
        ]);

        // Filter: Nama karyawan (search)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('karyawan', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('mentor', function ($q2) use ($search) {
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
        $pdf = Pdf::loadView('adminsdm.BehaviorIDP.RiwayatIDP.riwayat_pdf', [
            'idps' => $idps,
            'type_menu' => 'data-master',
            'waktuCetak' => $waktuCetak,
        ]);

        return $pdf->stream('Data-IDP-Terfilter.pdf');
    }
}
