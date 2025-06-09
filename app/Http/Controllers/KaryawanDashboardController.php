<?php

namespace App\Http\Controllers;

use App\Models\IDP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use App\Models\IdpKompetensiPengerjaan;
use Illuminate\Support\Facades\Log;
use App\Models\IdpKompetensi;
use App\Models\TemplateApplay;
use App\Notifications\PengerjaanBaruNotification;
use App\Notifications\PengerjaanDikirimUlangNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KaryawanDashboardController extends Controller
{
    public function index()
    {
        return view('karyawan.dashboard-karyawan', [
            'type_menu' => 'karyawan',
        ]);
    }
    public function indexKaryawan(Request $request)
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
            'idpKompetensis.metodeBelajars',
            'karyawan',
            'karyawan.jenjang',
            'karyawan.learningGroup',
            'rekomendasis'
        ])
            ->where('id_user', $user->id) // Ambil IDP hanya milik user login
            ->whereHas('rekomendasis', function ($q) {
                $q->whereNotNull('hasil_rekomendasi')
                    ->where('hasil_rekomendasi', '!=', '');
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('karyawan', function ($q) use ($search) {
                    $q->where('proyeksi_karir', 'like', "%$search%")
                        ->orWhere('id_karyawan', 'like', "%$search%");
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
            ->paginate(10);

        return view('karyawan.IDP.index', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'karyawan',
        ]);
    }
    public function showKaryawan($id, Request $request)
    {
        if ($request->has('notification_id')) {
            $notification = DatabaseNotification::find($request->notification_id);

            if ($notification && $notification->notifiable_id == Auth::id()) {
                $notification->markAsRead();
            }
        }
        // Mengambil data Divisi berdasarkan ID
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars', // relasi kompetensi beserta metode belajar
            'idpKompetensis.pengerjaans'
        ])->findOrFail($id);
        // Ambil id_pengerjaan dari query string jika ada
        $highlightPengerjaan = null;
        if ($request->has('pengerjaan')) {
            $highlightPengerjaan = IdpKompetensiPengerjaan::with([
                'idpKompetensi.kompetensi',
            ])->find($request->pengerjaan);
        }
        return view('karyawan.IDP.detail', [
            'idps' => $idps,
            'highlightPengerjaan' => $highlightPengerjaan,
            'type_menu' => 'karyawan',
        ]);
    }
    public function DetailKaryawan($id, Request $request)
    {
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars', // relasi kompetensi beserta metode belajar
            'idpKompetensis.pengerjaans'
        ])->findOrFail($id);
        // Ambil id_pengerjaan dari query string jika ada
        return view('karyawan.IDP.detail-menunggu', [
            'idps' => $idps,
            'type_menu' => 'karyawan',
        ]);
    }
    public function storeImplementasiSoft(Request $request, $id_idpKom)
    {
        $request->validate([
            'upload_hasil' => 'required|file|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png,csv|max:5120', // 20MB = 20*1024 KB = 20480
            'keterangan_hasil' => 'nullable|string',
        ]);

        // Simpan file dari input 'upload_hasil'
        $path = $request->file('upload_hasil')->store('implementasi', 'public');

        $idpKomPeng = new IdpKompetensiPengerjaan();
        $idpKomPeng->id_idpKom = $id_idpKom;
        $idpKomPeng->upload_hasil = $path;
        $idpKomPeng->keterangan_hasil = $request->input('keterangan_hasil');
        $idpKomPeng->status_pengerjaan = 'Menunggu Persetujuan';
        $idpKomPeng->save();
        $user = Auth::user();

        // Ambil relasi ke IDP dan mentor
        $idpKompetensi = IdpKompetensi::with('idp')->find($id_idpKom);
        if (!$idpKompetensi) {
            Log::error("IDP Kompetensi tidak ditemukan dengan id $id_idpKom");
            return redirect()->back()->with('error', 'IDP Kompetensi tidak ditemukan.');
        }
        $idp = $idpKompetensi->idp;
        if (!$idp) {
            Log::error("IDP tidak ditemukan untuk idp_kompetensi id $id_idpKom");
            return redirect()->back()->with('error', 'Data IDP tidak ditemukan.');
        }

        $mentor = $idp->mentor;
        if (!$mentor) {
            Log::error("Mentor tidak ditemukan untuk IDP ID {$idp->id}");
            return redirect()->back()->with('error', 'Mentor belum ditentukan.');
        }
        if ($idp->status_pengerjaan === 'Menunggu Tindakan') {
            $idp->status_pengerjaan = 'Sedang Dikerjakan';
            $idp->save();
        }

        // Debug log
        Log::info('Data dikirim ke notifikasi:', [
            'id_idp' => $idp->id_idp ?? 'NULL',
            'id_idpKom' => $idpKompetensi->id_idpKom ?? 'NULL',
            'id_idpKomPeng' => $idpKomPeng->id_idpKomPeng ?? 'NULL',
        ]);

        // Kirim notifikasi
        $mentor->notify(new PengerjaanBaruNotification([
            'id_idp' => $idp->id_idp,
            'id_idpKom' => $idpKompetensi->id_idpKom,
            'nama_karyawan' => $user->name,
            'id_idpKomPeng' => $idpKomPeng->id_idpKomPeng,
            'untuk_role' => 'mentor',
        ]));
        return redirect()->back()->with('success', 'File dan data berhasil disimpan.');
    }
    public function storeImplementasiHard(Request $request, $id_idpKom)
    {
        Log::info('Request masuk ke storeImplementasiHard', [
            'id_idpKom' => $id_idpKom,
            'file' => $request->file('upload_hasil'),
            'keterangan' => $request->input('keterangan_hasil'),
        ]);

        $request->validate([
            'upload_hasil' => 'required|file|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png,csv|max:5120',
            'keterangan_hasil' => 'nullable|string',
        ]);

        // Simpan file
        $path = $request->file('upload_hasil')->store('implementasi', 'public');

        // Simpan pengerjaan
        $idpKomPeng = new IdpKompetensiPengerjaan();
        $idpKomPeng->id_idpKom = $id_idpKom;
        $idpKomPeng->upload_hasil = $path;
        $idpKomPeng->keterangan_hasil = $request->input('keterangan_hasil');
        $idpKomPeng->status_pengerjaan = 'Menunggu Persetujuan';
        $idpKomPeng->save();
        $user = Auth::user();

        // Ambil relasi ke IDP dan mentor
        $idpKompetensi = IdpKompetensi::with('idp')->find($id_idpKom);

        if (!$idpKompetensi) {
            Log::error("IDP Kompetensi tidak ditemukan dengan id $id_idpKom");
            return redirect()->back()->with('error', 'IDP Kompetensi tidak ditemukan.');
        }

        $idp = $idpKompetensi->idp;
        if (!$idp) {
            Log::error("IDP tidak ditemukan untuk idp_kompetensi id $id_idpKom");
            return redirect()->back()->with('error', 'Data IDP tidak ditemukan.');
        }

        $mentor = $idp->mentor;
        if (!$mentor) {
            Log::error("Mentor tidak ditemukan untuk IDP ID {$idp->id}");
            return redirect()->back()->with('error', 'Mentor belum ditentukan.');
        }
        if ($idp->status_pengerjaan === 'Menunggu Tindakan') {
            $idp->status_pengerjaan = 'Sedang Dikerjakan';
            $idp->save();
        }

        // Debug log
        Log::info('Data dikirim ke notifikasi:', [
            'id_idp' => $idp->id ?? 'NULL',
            'id_idpKom' => $idpKompetensi->id ?? 'NULL',
            'id_idpKomPeng' => $idpKomPeng->id ?? 'NULL',
        ]);

        // Kirim notifikasi
        $mentor->notify(new PengerjaanBaruNotification([
            'id_idp' => $idp->id_idp,
            'id_idpKom' => $idpKompetensi->id_idpKom,
            'nama_karyawan' => $user->name,
            'id_idpKomPeng' => $idpKomPeng->id_idpKomPeng,
            'untuk_role' => 'mentor',
        ]));

        return redirect()->back()->with('success', 'File dan data berhasil disimpan.');
    }
    public function uploadUlang(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'upload_hasil' => 'required|file|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png,csv|max:5120', // max 5MB
            'keterangan_hasil' => 'required|string|max:1000',
        ]);

        // Cari data pengerjaan berdasarkan ID, fail jika tidak ditemukan
        $pengerjaan = IdpKompetensiPengerjaan::findOrFail($id);

        // Simpan file jika ada
        if ($request->hasFile('upload_hasil')) {
            // Hapus file lama jika ada agar storage tidak penuh
            if ($pengerjaan->upload_hasil && Storage::disk('public')->exists($pengerjaan->upload_hasil)) {
                Storage::disk('public')->delete($pengerjaan->upload_hasil);
            }

            // Simpan file baru dan update path di DB
            $path = $request->file('upload_hasil')->store('uploads/hasil', 'public');
            $pengerjaan->upload_hasil = $path;
        }

        // Update keterangan hasil dan status pengerjaan
        $pengerjaan->keterangan_hasil = $request->keterangan_hasil;
        $pengerjaan->status_pengerjaan = 'Menunggu Persetujuan'; // Reset status pengerjaan
        $pengerjaan->save();
        $user = Auth::user();

        // Ambil data IDP dan mentor terkait
        $idpKompetensi = IdpKompetensi::with('idp')->find($pengerjaan->id_idpKom);
        if (!$idpKompetensi || !$idpKompetensi->idp || !$idpKompetensi->idp->mentor) {
            Log::warning("Mentor tidak ditemukan saat upload ulang oleh {$user->name}");
        } else {
            $mentor = $idpKompetensi->idp->mentor;

            // Kirim notifikasi ke mentor
            $mentor->notify(new PengerjaanDikirimUlangNotification([
                'id_idp' => $idpKompetensi->idp->id_idp,
                'id_idpKom' => $idpKompetensi->id_idpKom,
                'nama_karyawan' => $user->name,
                'id_idpKomPeng' => $pengerjaan->id_idpKomPeng,
                'untuk_role' => 'mentor',
            ]));
        }
        // Kalau kamu mau respon JSON (misal dipakai AJAX/fetch), pakai ini:
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Implementasi berhasil dikirim ulang.',
            ]);
        }

        // Kalau request biasa redirect balik dengan flash message
        return redirect()->back()->with('success', 'Implementasi berhasil dikirim ulang.');
    }
    public function bankIdp(Request $request)
    {
        $user = Auth::user();
        $jenjang_id = $user->id_jenjang;
        $lgId = $user->learningGroup->id_LG;
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $id_LG = $request->query('lg');
        $tahun = $request->query('tahun');
        $appliedIdpIds = DB::table('idp_template_applies')
            ->where('id_user', $user->id)
            ->pluck('id_idp_template')
            ->toArray();
        $listTahun = IDP::whereNotNull('waktu_mulai')
            ->selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        $mentors = User::whereHas('roles', fn($q) => $q->where('user_roles.id_role', 3))->get();

        $idps = IDP::with([
            'jenjang',
            'learningGroup',
            'karyawan',
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars',
            'idpKompetensis.pengerjaans',
        ])
            ->where('is_template', true)
            ->where('id_jenjang', $jenjang_id)
            ->where('id_LG', $lgId)
            ->whereNotIn('id_idp', $appliedIdpIds) // ⬅ ini baris tambahan untuk menyembunyikan IDP yang sudah didaftar
            ->when($search, function ($query, $search) {
                // Search di kolom proyeksi_karir langsung di tabel idp
                return $query->where('proyeksi_karir', 'like', "%$search%");
            })
            ->when($id_LG, function ($query, $id_LG) {
                return $query->where('id_LG', $id_LG);
            })
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('waktu_mulai', $tahun);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('karyawan.IDP.bank-idp', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'id_jenjang' => $jenjang_id,
            'id_LG' => $lgId,
            'mentors' => $mentors,
            'type_menu' => 'karyawan',
        ]);
    }
    public function applyBankIdp(Request $request)
    {
        $request->validate([
            'id_idp_template' => 'required|exists:idps,id_idp',
            'id_mentor' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        $templateIDP = IDP::where('id_idp', $request->id_idp_template)
            ->where('is_template', 1)
            ->firstOrFail();
        // Cek apakah user sudah pernah daftar IDP ini sebelumnya
        $sudahDaftar = IDP::where('id_idp_template_asal', $templateIDP->id_idp)
            ->where('id_user', $user->id)
            ->exists();

        if ($sudahDaftar) {
            return redirect()->back()->with('msg-error', 'Anda sudah mendaftar IDP ini!');
        }
        // Cek apakah template masih terbuka
        if (!$templateIDP->is_open) {
            return back()->withErrors(['msg-success' => 'Template IDP sudah ditutup untuk pendaftaran.']);
        }

        // Cek kuota apply
        if ($templateIDP->max_applies !== null && $templateIDP->current_applies >= $templateIDP->max_applies) {
            return back()->withErrors(['msg-success' => 'Kuota pendaftaran untuk template IDP ini sudah penuh.']);
        }
        // 1. Simpan apply record
        $apply = TemplateApplay::create([
            'id_idp_template' => $templateIDP->id_idp,
            'id_user' => $user->id,
            'id_mentor' => $request->id_mentor,
            'id_jenjang' => $templateIDP->id_jenjang,
            'applied_at' => now(),
            'id_jabatan' => $user->id_jabatan,
            'id_angkatanpsp' => $user->id_angkatanpsp,
            'id_divisi' => $user->id_divisi,
            'id_penempatan' => $user->id_penempatan,
            'id_LG' => $user->id_LG,
            'id_semester' => $user->id_semester,

        ]);

        // 2. Buat IDP baru hasil apply user
        $idp = IDP::create([
            'id_user' => $user->id,
            'id_mentor' => $request->id_mentor,
            'id_supervisor' => $templateIDP->id_supervisor,
            'id_semester' => $templateIDP->id_semester,
            'id_jenjang' => $templateIDP->id_jenjang,
            'id_jabatan' => $user->id_jabatan,
            'id_angkatanpsp' => $user->id_angkatanpsp,
            'id_divisi' => $user->id_divisi,
            'id_penempatan' => $user->id_penempatan,
            'id_LG' => $user->id_LG,
            'proyeksi_karir' => $templateIDP->proyeksi_karir,
            'waktu_mulai' => $templateIDP->waktu_mulai,
            'waktu_selesai' => $templateIDP->waktu_selesai,
            'deskripsi_idp' => $templateIDP->deskripsi_idp,
            'status_approval_mentor' => $templateIDP->status_approval_mentor,
            'status_pengajuan_idp' => $templateIDP->status_pengajuan_idp,
            'status_pengerjaan' => $templateIDP->status_pengerjaan,
            'is_template' => 0,
            'id_idp_template_asal' => $templateIDP->id_idp,
        ]);

        // 3. Copy kompetensi + metode belajar ke IDP baru
        $templateKompetensis = $templateIDP->idpKompetensis;

        foreach ($templateKompetensis as $tempKom) {
            $idpKom = IdpKompetensi::create([
                'id_idp' => $idp->id_idp,
                'id_kompetensi' => $tempKom->id_kompetensi,
                'sasaran' => $tempKom->sasaran,
                'aksi' => $tempKom->aksi,
            ]);

            $metodeBelajars = $tempKom->metodeBelajars;
            if ($metodeBelajars->isNotEmpty()) {
                $idpKom->metodeBelajars()->attach($metodeBelajars->pluck('id_metodeBelajar')->toArray());
            }
        }
        // Update current_applies
        $currentCount = IDP::where('id_idp_template_asal', $templateIDP->id_idp)->count();
        $templateIDP->current_applies = $currentCount;
        $templateIDP->save();

        return redirect()->back()->with('msg-success', 'Selamat Anda Berhasil Mendaftar IDP ini!');
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
            ->where('id_user', $user->id) // Ambil IDP hanya milik user login
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

        return view('karyawan.RiwayatIDP.riwayat-idp', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'karyawan',
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
        return view('karyawan.RiwayatIDP.detailRiwayat', [
            'idps'    => $idps,
            'type_menu' => 'karyawan',
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
            ->where('id_user', $user->id); // Ambil IDP hanya milik user login


        // Filter: Nama karyawan (search)
        if ($request->filled('search')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
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
        $pdf = Pdf::loadView('karyawan.RiwayatIDP.riwayat_pdf', [
            'idps' => $idps,
            'type_menu' => 'karyawan',
            'waktuCetak' => $waktuCetak,
        ]);

        return $pdf->stream('Data-IDP.pdf');
    }
}
