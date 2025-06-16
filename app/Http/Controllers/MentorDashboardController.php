<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use Illuminate\Http\Request;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use App\Models\IdpKompetensiPengerjaan;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\PenilaianDiperbaruiNotification;
use App\Notifications\VerifikasiIDPNotification;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\IdpRekomendasi;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MentorDashboardController extends Controller
{
    public function index()
    {
        $idpIds = IDP::where('id_mentor', Auth::id())
            ->where('is_template', false) // pastikan bukan bank IDP
            ->pluck('id_idp');
        $jumlahIDPGiven = IDP::where('is_template', false)
            ->where('id_mentor', Auth::id()) // hanya milik user yang sedang login
            ->count();
        $jumlahRekomendasiBelumMuncul = IDP::where('is_template', false) // hanya IDP biasa, bukan bank
            ->where('id_mentor', Auth::id()) // hanya milik karyawan yang login
            ->where(function ($query) {
                $query->doesntHave('rekomendasis') // tidak ada rekomendasi sama sekali
                    ->orWhereHas('rekomendasis', function ($q) {
                        $q->whereNull('hasil_rekomendasi') // ada rekomendasi tapi belum ada hasilnya
                            ->orWhere('hasil_rekomendasi', '');
                    });
            })
            ->count();
        // Hitung berdasarkan hasil rekomendasi
        $jumlahDisarankan = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->where('hasil_rekomendasi', 'Disarankan')
            ->count();

        $jumlahDisarankanDenganPengembangan = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->where('hasil_rekomendasi', 'Disarankan dengan Pengembangan')
            ->count();

        $jumlahTidakDisarankan = IdpRekomendasi::whereIn('id_idp', $idpIds)
            ->where('hasil_rekomendasi', 'Tidak Disarankan')
            ->count();
        $karyawanId = Auth::id(); // ID user login (karyawan)

        $jumlahMenungguPersetujuan = IDP::where('id_mentor', $karyawanId)
            ->where('status_approval_mentor', 'Menunggu Persetujuan')
            ->where('is_template', false)
            ->count();
        $user = Auth::user();
        $jumlahIDPRevisi = IDP::where('id_mentor', $karyawanId)
            ->where('status_pengajuan_idp', 'Revisi')
            ->where('is_template', false)
            ->count();
        $jumlahIdpTidakDisetujui = IDP::where('id_mentor', $karyawanId)
            ->where('status_pengajuan_idp', 'Tidak Disetujui')
            ->where('is_template', false)
            ->count();
        $jumlahIdpMenungguPersetujuan = IDP::where('id_mentor', $karyawanId)
            ->where('status_pengajuan_idp', 'Menunggu Persetujuan')
            ->where('is_template', false)
            ->count();
        $user = Auth::user();
        $rekomendasiData = IdpRekomendasi::with('idp.karyawan.roles')
            ->get()
            ->filter(function ($item) use ($user) {
                return $item->idp
                    && $item->idp->id_mentor == $user->id // hanya idp milik user login
                    && $item->idp->karyawan
                    && $item->idp->karyawan->roles->contains('id_role', 4);
            })
            ->map(function ($item) {
                return [
                    'x' => $item->nilai_akhir_hard,
                    'y' => $item->nilai_akhir_soft,
                    'label' => ($item->idp->karyawan->name ?? 'Tidak Diketahui') . ' - ' . ($item->idp->proyeksi_karir ?? '-'),
                ];
            })
            ->values();
        $topKaryawan = IdpRekomendasi::with(['idp.karyawan'])
            ->where('hasil_rekomendasi', 'Disarankan')
            ->whereHas('idp', function ($query) use ($user) {
                $query->where('id_mentor', $user->id);
            })
            ->orderByDesc('nilai_akhir_soft')
            ->orderByDesc('nilai_akhir_hard')
            ->take(5)
            ->get();
        $jenjangData = IDP::select('id_jenjang', DB::raw('count(*) as total'))
            ->where('id_mentor', $user->id)
            ->groupBy('id_jenjang')
            ->with('jenjang')
            ->get();

        // Buat array kosong jika tidak ada data
        $jenjangLabels = [];
        $jenjangTotals = [];

        if ($jenjangData->isNotEmpty()) {
            foreach ($jenjangData as $data) {
                $jenjangLabels[] = $data->jenjang ? $data->jenjang->nama_jenjang : 'Tidak diketahui';
                $jenjangTotals[] = (int) $data->total;
            }
        }
        $LGData = IDP::select('id_LG', DB::raw('count(*) as total'))
            ->where('id_mentor', $user->id)
            ->groupBy('id_LG')
            ->with('learningGroup')
            ->get();

        // Buat array kosong jika tidak ada data
        $LGLabels = [];
        $LGTotals = [];

        if ($LGData->isNotEmpty()) {
            foreach ($LGData as $data) {
                $LGLabels[] = $data->learningGroup ? $data->learningGroup->nama_LG : 'Tidak diketahui';
                $LGTotals[] = (int) $data->total;
            }
        }

        $totalBelumEvaluasiPasca = Idp::where('id_mentor', Auth::id())
            ->whereHas('rekomendasis', function ($query) {
                $query->whereIn('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan']);
            })
            ->whereDoesntHave('evaluasiIdp', function ($query) {
                $query->where('jenis_evaluasi', 'pasca')
                    ->where('sebagai_role', 'mentor');
            })
            ->count();
        $mentor = Auth::user();

        $idpsBelumDievaluasi = IDP::whereDate('waktu_mulai', '<=', now())
            ->whereDate('waktu_selesai', '>=', now())
            ->whereDoesntHave('rekomendasis') // ✅ Tambahkan ini
            ->whereHas('user', function ($q) use ($mentor) {
                $q->where('id_mentor', $mentor->id);
            })
            ->whereDoesntHave('evaluasiIdp', function ($q) use ($mentor) {
                $q->where('jenis_evaluasi', 'onboarding')
                    ->where('id_user', $mentor->id);
            })
            ->with('user')
            ->limit(5) // Biar tidak terlalu panjang di dashboard
            ->get();

        return view('mentor.dashboard-mentor', [
            'type_menu' => 'dashboard',
            'jumlahIDPGiven' => $jumlahIDPGiven,
            'jumlahRekomendasiBelumMuncul' => $jumlahRekomendasiBelumMuncul,
            'jumlahDisarankan' => $jumlahDisarankan,
            'jumlahDisarankanDenganPengembangan' => $jumlahDisarankanDenganPengembangan,
            'jumlahTidakDisarankan' => $jumlahTidakDisarankan,
            'jumlahMenungguPersetujuan' => $jumlahMenungguPersetujuan,
            'dataPoints' => $rekomendasiData,
            'topKaryawan' => $topKaryawan,
            'jumlahIDPRevisi' => $jumlahIDPRevisi,
            'jumlahIdpTidakDisetujui' => $jumlahIdpTidakDisetujui,
            'jenjangLabels' => $jenjangLabels,
            'jenjangTotals' => $jenjangTotals,
            'LGLabels' => $LGLabels,
            'LGTotals' => $LGTotals,
            'totalBelumEvaluasiPasca' => $totalBelumEvaluasiPasca,
            'idpsBelumDievaluasi' => $idpsBelumDievaluasi,
        ]);
    }
    public function indexMentor(Request $request)
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
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars'
        ])
            ->where('id_mentor', $user->id) // Ambil IDP hanya milik user login
            ->when($search, function ($query, $search) {
                return $query->whereHas('mentor', function ($q) use ($search) {
                    $q->where('proyeksi_karir', 'like', "%$search%")
                        ->orWhere('id_mentor', 'like', "%$search%");
                });
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->whereHas('mentor', function ($q) use ($id_jenjang) {
                    $q->where('id_jenjang', $id_jenjang);
                });
            })
            ->when($id_LG, function ($query, $id_LG) {
                return $query->whereHas('mentor', function ($q) use ($id_LG) {
                    $q->where('lg', $id_LG);
                });
            })
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('waktu_mulai', $tahun);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('mentor.IDP.index', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'mentor',
        ]);
    }
    public function showMentor($id, Request $request)
    {
        if ($request->has('notification_id')) {
            $notification = DatabaseNotification::find($request->notification_id);

            if ($notification && $notification->notifiable_id == Auth::id()) {
                $notification->markAsRead();
            }
        }
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
        return view('mentor.IDP.detail', [
            'idps' => $idps,
            'pengerjaans' => $pengerjaans,
            'highlightPengerjaan' => $highlightPengerjaan,
            'type_menu' => 'mentor',
        ]);
    }
    public function updatePenilaian(Request $request, $id)
    {
        $request->validate([
            'status_pengerjaan' => 'required',
            'saran' => 'nullable|string'
        ]);

        $pengerjaan = IdpKompetensiPengerjaan::findOrFail($id);
        $pengerjaan->status_pengerjaan = $request->status_pengerjaan;
        $pengerjaan->saran = $request->saran;
        $pengerjaan->save();
        $user = Auth::user();
        // Ambil karyawan dari relasi
        $karyawan = $pengerjaan->idpKompetensi->idp->karyawan;

        // Buat pesan sesuai status
        $pesanStatus = match ($request->status_pengerjaan) {
            'Menunggu Persetujuan' => 'IDP Anda sedang menunggu persetujuan dari mentor.',
            'Disetujui Mentor' => 'Pengerjaan IDP Anda telah disetujui oleh mentor.',
            'Ditolak Mentor' => 'Pengerjaan IDP Anda ditolak oleh mentor.',
            'Revisi Mentor' => 'Pengerjaan IDP Anda perlu direvisi sesuai saran mentor.',
            default => 'Penilaian IDP Anda telah diperbarui oleh mentor.'
        };

        // Kirim notifikasi
        $karyawan->notify(new PenilaianDiperbaruiNotification([
            'id_idp' => $pengerjaan->idpKompetensi->idp->id_idp,
            'id_idpKomPeng' => $pengerjaan->id_idpKomPeng,
            'status' => $request->status_pengerjaan,
            'saran' => $request->saran,
            'nama_mentor' => $user->name,
            'untuk_role' => 'karyawan',
            'message' => $pesanStatus,
        ]));
        return redirect()->back()->with('msg-success', 'Penilaian berhasil diperbarui.');
    }
    public function verifikasi($id)
    {
        $idps = IDP::with(['karyawan'])->findOrFail($id);

        // Pastikan hanya mentor yang berwenang bisa buka
        if ($idps->id_mentor != Auth::id()) {
            abort(403, 'Anda tidak berhak memverifikasi IDP ini.');
        }

        return view('mentor.IDP.verifikasi', [
            'idps' => $idps,
            'type_menu' => 'mentor',
        ]);
    }

    public function updateVerifikasi(Request $request, $id)
    {
        $request->validate([
            'status_approval_mentor' => 'required|in:Menunggu Persetujuan,Disetujui,Ditolak',
            'status_pengajuan_idp' => 'required|in:Menunggu Persetujuan,Revisi,Disetujui,Tidak Disetujui',
            'saran_idp' => 'nullable|string|max:1000',
        ]);

        $idps = IDP::findOrFail($id);

        if ($idps->id_mentor != Auth::id()) {
            abort(403, 'Anda tidak berhak memverifikasi IDP ini.');
        }

        $idps->status_approval_mentor = $request->status_approval_mentor;
        $idps->status_pengajuan_idp = $request->status_pengajuan_idp;
        $idps->saran_idp = $request->saran_idp;
        $idps->save();
        $karyawan = $idps->karyawan;
        $mentor = Auth::user();

        // Gabungkan pesan status approval dan pengajuan
        $pesan = "IDP Anda telah diverifikasi oleh mentor ({$mentor->name}). "
            . "Status Approval Mentor: {$request->status_approval_mentor}. "
            . "Status Pengajuan: {$request->status_pengajuan_idp}.";

        // Kirim notifikasi ke karyawan
        $karyawan->notify(new VerifikasiIDPNotification([
            'id_idp' => $idps->id_idp,
            'status_pengajuan_idp' => $request->status_pengajuan_idp,
            'status_approval_mentor' => $request->status_approval_mentor,
            'saran_idp' => $request->saran_idp,
            'nama_mentor' => $mentor->name,
            'untuk_role' => 'karyawan',
            'message' => $pesan,
        ]));

        return redirect()->route('mentor.IDP.indexMentor', $id)
            ->with('msg-success', 'Verifikasi berhasil disimpan.');
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

        return view('mentor.RiwayatIDP.riwayat', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'mentor',
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
        return view('mentor.RiwayatIDP.detailRiwayat', [
            'idps'    => $idps,
            'type_menu' => 'mentor',
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
            ->where('id_mentor', $user->id); // Ambil IDP hanya milik user login


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
        $pdf = Pdf::loadView('mentor.RiwayatIDP.riwayat_pdf', [
            'idps' => $idps,
            'type_menu' => 'mentor',
            'waktuCetak' => $waktuCetak,
        ]);

        return $pdf->stream('Data-IDP.pdf');
    }
}
