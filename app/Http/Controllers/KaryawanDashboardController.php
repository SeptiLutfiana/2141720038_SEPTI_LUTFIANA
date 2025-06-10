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
use App\Models\IdpRekomendasi;
use App\Models\Jabatan;
use App\Models\Penempatan;
use App\Models\Divisi;
use App\Models\Kompetensi;
use App\Models\MetodeBelajar;
use FFI\Exception;

class KaryawanDashboardController extends Controller
{
    public function index()
    {
        $idpIds = IDP::where('id_user', Auth::id())
            ->where('is_template', false) // pastikan bukan bank IDP
            ->pluck('id_idp');
        $jumlahIDPGiven = IDP::where('is_template', false)
            ->where('id_user', Auth::id()) // hanya milik user yang sedang login
            ->count();
        $jumlahRekomendasiBelumMuncul = IDP::where('is_template', false) // hanya IDP biasa, bukan bank
            ->where('id_user', Auth::id()) // hanya milik karyawan yang login
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

        $jumlahMenungguPersetujuan = IDP::where('id_user', $karyawanId)
            ->where('status_approval_mentor', 'Menunggu Persetujuan')
            ->where('is_template', false)
            ->count();
        $user = Auth::user();
        $jumlahIDPRevisi = IDP::where('id_user', $karyawanId)
            ->where('status_pengajuan_idp', 'Revisi')
            ->where('is_template', false)
            ->count();
        $jumlahIdpTidakDisetujui = IDP::where('id_user', $karyawanId)
            ->where('status_pengajuan_idp', 'Tidak Disetujui')
            ->where('is_template', false)
            ->count();
        $jumlahIdpMenungguPersetujuan = IDP::where('id_user', $karyawanId)
            ->where('status_pengajuan_idp', 'Menunggu Persetujuan')
            ->where('is_template', false)
            ->count();
        $user = Auth::user();
        $rekomendasiData = IdpRekomendasi::with('idp.karyawan.roles')
            ->get()
            ->filter(function ($item) use ($user) {
                return $item->idp
                    && $item->idp->id_user == $user->id // hanya idp milik user login
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
                $query->where('id_user', $user->id);
            })
            ->orderByDesc('nilai_akhir_soft')
            ->orderByDesc('nilai_akhir_hard')
            ->take(5)
            ->get();
        return view('karyawan.dashboard-karyawan', [
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
            // 'jumlahIdpMenungguPersetujuan' =>$jumlahIdpMenungguPersetujuan,
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
        $pdf = Pdf::loadView('karyawan.RiwayatIDP.riwayat_pdf', [
            'idps' => $idps,
            'type_menu' => 'karyawan',
            'waktuCetak' => $waktuCetak,
        ]);

        return $pdf->stream('Data-IDP.pdf');
    }
    public function create()
    {
        // Ambil data user berdasarkan role
        $mentors = User::whereHas('roles', function ($query) {
            $query->where('user_roles.id_role', 3); // Role mentor
        })->get();

        $supervisors = User::whereHas('roles', function ($query) {
            $query->where('user_roles.id_role', 2); // Role supervisor
        })->get();

        $karyawans = User::whereHas('roles', function ($query) {
            $query->where('user_roles.id_role', 4); // Role karyawan
        })->get();

        // $semesters = Semester::all();
        $listJenjang = Jenjang::all();  // Ambil daftar jenjang
        $listJabatan = Jabatan::all();  // Ambil daftar jenjang
        $listLG = LearingGroup::all();  // Ambil daftar learning group
        $listDivisi = Divisi::all();
        $listPenempatan = Penempatan::all();
        $kompetensis = Kompetensi::all();
        $metodeBelajars = MetodeBelajar::all();
        return view('karyawan.IDP.create', [
            'mentors' => $mentors,
            'supervisors' => $supervisors,
            'karyawans' => $karyawans,
            'listJabatan' => $listJabatan,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listDivisi' => $listDivisi,
            'listPenempatan' => $listPenempatan,
            'kompetensis' => $kompetensis,
            'metodeBelajars' => $metodeBelajars,
            'type_menu' => 'karyawan'
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'proyeksi_karir' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'id_mentor' => 'nullable|exists:users,id',
            'id_supervisor' => 'required|exists:users,id',
            'deskripsi_idp' => 'nullable|string',
            'saran_idp' => 'nullable|string',
            'kompetensi' => 'required|array',
            'kompetensi.*.id_kompetensi' => 'required|exists:kompetensis,id_kompetensi',
            'kompetensi.*.id_metode_belajar' => 'required|array|min:1',
            'kompetensi.*.id_metode_belajar.*' => 'exists:metode_belajars,id_metodeBelajar',
            'kompetensi.*.sasaran' => 'required|string',
            'kompetensi.*.aksi' => 'required|string',
            'kompetensi.*.peran' => 'required|in:umum,utama,kunci_core,kunci_bisnis,kunci_enabler',
        ]);

        DB::transaction(function () use ($request, $user) {
            $idp = IDP::create([
                'id_user' => $user->id,
                'id_mentor' => $request->id_mentor,
                'id_supervisor' => $request->id_supervisor,
                'proyeksi_karir' => $request->proyeksi_karir,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'status_approval_mentor' => 'Menunggu Persetujuan',
                'status_pengajuan_idp' => 'Menunggu Persetujuan',
                'status_pengerjaan' => 'Menunggu Tindakan',
                'is_template' => false,
                'saran_idp' => $request->saran_idp,
                'deskripsi_idp' => $request->deskripsi_idp,

                // dari data user
                'id_jenjang' => $user->jenjang->id_jenjang,
                'id_jabatan' => $user->jabatan->id_jabatan,
                'id_LG' => $user->learningGroup->id_LG,
                'id_divisi' => $user->divisi->id_divisi,
                'id_penempatan' => $user->penempatan->id_penempatan,
                'id_semester' => $user->semester->id_semester,
                'id_angkatanpsp' => $user->angkatanPsp->id_angkatanpsp
            ]);

            foreach ($request->kompetensi as $item) {
                $idpKompetensiId = DB::table('idp_kompetensis')->insertGetId([
                    'id_idp' => $idp->id_idp,
                    'id_kompetensi' => $item['id_kompetensi'],
                    'sasaran' => $item['sasaran'],
                    'aksi' => $item['aksi'],
                    'peran' => $item['peran'],
                ]);

                foreach ($item['id_metode_belajar'] as $idMetode) {
                    DB::table('idp_kompetensi_metode_belajars')->insert([
                        'id_idpKom' => $idpKompetensiId,
                        'id_metodeBelajar' => $idMetode,
                    ]);
                }
            }
        });

        return redirect()->route('karyawan.IDP.indexKaryawan')
            ->with('msg-success', 'IDP berhasil diajukan. Menunggu persetujuan.');
    }
    public function editIdp($id)
    {
        $idp = IDP::with(['idpKompetensis.metodeBelajars'])->findOrFail($id);

        // Ambil data yang dibutuhkan untuk form
        $mentors = User::whereHas('roles', fn($q) => $q->where('user_roles.id_role', 3))->get();
        $supervisors = User::whereHas('roles', fn($q) => $q->where('user_roles.id_role', 2))->get();
        $karyawans = User::whereHas('roles', fn($q) => $q->where('user_roles.id_role', 4))->get();
        $listJenjang = Jenjang::all();
        $listJabatan = Jabatan::all();
        $listLG = LearingGroup::all();
        $listDivisi = Divisi::all();
        $listPenempatan = Penempatan::all();
        $kompetensi = Kompetensi::all();
        $metodeBelajars = MetodeBelajar::all();

        return view('karyawan.IDP.edit', [
            'idp' => $idp,
            'mentors' => $mentors,
            'supervisors' => $supervisors,
            'karyawans' => $karyawans,
            'listJabatan' => $listJabatan,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listDivisi' => $listDivisi,
            'listPenempatan' => $listPenempatan,
            'kompetensi' => $kompetensi,
            'metodeBelajars' => $metodeBelajars,
            'type_menu' => 'karyawan'
        ]);
    }
    public function updateIdp(Request $request, $id)
    {
        $idp = IDP::findOrFail($id);
        Log::debug('Data request yang diterima:', $request->all());

        $validated = $request->validate([
            'proyeksi_karir' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'deskripsi_idp' => 'nullable|string',
            'id_mentor' => 'required|exists:users,id',
            'id_supervisor' => 'required|exists:users,id',
            'kompetensi' => 'nullable|array',
            'kompetensi.*.id' => 'nullable|integer',
            'kompetensi.*.id_kompetensi' => 'nullable|integer|exists:kompetensis,id_kompetensi', // Hanya untuk yang baru
            'kompetensi.*.sasaran' => 'required|string',
            'kompetensi.*.aksi' => 'required|string',
            'kompetensi.*.peran' => 'nullable|string',
            'kompetensi.*.id_metode_belajar' => 'nullable|array',
            'kompetensi.*.id_metode_belajar.*' => 'integer|exists:metode_belajars,id_metodeBelajar',
        ]);

        try {
            DB::transaction(function () use ($idp, $validated, $request) {
                Log::debug('Memulai transaksi database.');
                // 1. Update data IDP utama
                // Simpan ID mentor lama sebelum update
                $mentorLama = $idp->id_mentor;

                // Siapkan data update
                $updateData = [
                    'proyeksi_karir' => $validated['proyeksi_karir'],
                    'waktu_mulai' => $validated['waktu_mulai'],
                    'waktu_selesai' => $validated['waktu_selesai'],
                    'deskripsi_idp' => $validated['deskripsi_idp'] ?? null,
                    'id_mentor' => $validated['id_mentor'],
                    'id_supervisor' => $validated['id_supervisor'],
                    'status_pengajuan_idp' => 'Menunggu Persetujuan',
                ];

                // Jika mentor diganti, reset status persetujuan mentor
                if ($mentorLama != $validated['id_mentor']) {
                    $updateData['status_approval_mentor'] = 'Menunggu Persetujuan';
                }

                $idp->update($updateData);
                Log::debug("IDP dengan ID {$idp->id_idp} berhasil diperbarui.");

                // Akan menyimpan id_idpKom dari kompetensi yang sudah ada DAN BARU DIBUAT yang berhasil diproses
                $submittedIdpKompetensiIds = [];

                // 2. Proses kompetensi jika ada
                if (isset($validated['kompetensi']) && is_array($validated['kompetensi'])) {
                    Log::debug('Mulai memproses item kompetensi dari request.');
                    foreach ($validated['kompetensi'] as $key => $kompetensiData) {

                        // Periksa apakah ini kompetensi BARU (kuncinya dimulai dengan 'new_')
                        if (str_starts_with($key, 'new_')) {
                            Log::debug("Memproses kompetensi BARU dengan key: {$key}", $kompetensiData);
                            try {
                                $newIdpKompetensi = IDPKompetensi::create([
                                    'id_idp' => $idp->id_idp,
                                    'id_kompetensi' => $kompetensiData['id_kompetensi'], // ID master kompetensi
                                    'sasaran' => $kompetensiData['sasaran'],
                                    'aksi' => $kompetensiData['aksi'],
                                    'peran' => $kompetensiData['peran'] ?? 'umum',

                                ]);
                                Log::debug("Kompetensi baru berhasil dibuat dengan ID: {$newIdpKompetensi->id_idpKom}");

                                // --- BARIS KRITIS YANG PERLU DITAMBAHKAN ---
                                // Tambahkan ID kompetensi BARU ke array $submittedIdpKompetensiIds
                                // Ini penting agar tidak dihapus di langkah selanjutnya
                                $submittedIdpKompetensiIds[] = $newIdpKompetensi->id_idpKom;
                                // --------------------------------------------

                                // Sinkronkan metode belajar untuk kompetensi baru
                                $metodeBelajarIds = $kompetensiData['id_metode_belajar'] ?? [];
                                if (!empty($metodeBelajarIds)) {
                                    $newIdpKompetensi->metodeBelajars()->sync($metodeBelajarIds);
                                    Log::debug("Metode Belajar untuk kompetensi baru ID: {$newIdpKompetensi->id_idpKom} disinkronkan dengan ID: " . implode(', ', $metodeBelajarIds));
                                } else {
                                    Log::debug("Tidak ada metode belajar yang disubmit untuk kompetensi baru ID: {$newIdpKompetensi->id_idpKom}");
                                }
                            } catch (Exception $e) {
                                Log::error("Gagal membuat atau menyinkronkan kompetensi BARU dengan key {$key}. Error: " . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
                                throw $e; // Re-throw the exception to trigger rollback
                            }
                        }
                        // Tangani pembaruan kompetensi yang SUDAH ADA (kuncinya adalah angka atau 'id' ada)
                        else if (isset($kompetensiData['id'])) { // Periksa 'id' (id_idpKom)
                            $actualIdpKompetensiId = $kompetensiData['id']; // Ini adalah id_idpKom

                            Log::debug("Memproses kompetensi EXISTING dengan ID: {$actualIdpKompetensiId}", $kompetensiData);

                            try {
                                // Pastikan kompetensi yang akan diupdate adalah milik IDP ini
                                $idpKompetensi = IDPKompetensi::where('id_idpKom', $actualIdpKompetensiId)
                                    ->where('id_idp', $idp->id_idp) // Pastikan milik IDP ini
                                    ->first();

                                if ($idpKompetensi) {
                                    Log::debug("Kondisi terpenuhi untuk ID: {$actualIdpKompetensiId}. Mencoba update.");

                                    $idpKompetensi->update([
                                        'sasaran' => $kompetensiData['sasaran'],
                                        'aksi' => $kompetensiData['aksi'],
                                        'peran' => $kompetensiData['peran'] ?? 'umum',
                                    ]);
                                    Log::debug("Sasaran diperbarui menjadi: {$kompetensiData['sasaran']} untuk ID: {$actualIdpKompetensiId}");
                                    Log::debug("Aksi diperbarui menjadi: {$kompetensiData['aksi']} untuk ID: {$actualIdpKompetensiId}");

                                    // Sinkronkan metode belajar untuk kompetensi existing
                                    $metodeBelajarIds = $kompetensiData['id_metode_belajar'] ?? [];
                                    $idpKompetensi->metodeBelajars()->sync($metodeBelajarIds);
                                    Log::debug("Metode Belajar disinkronkan untuk ID: {$actualIdpKompetensiId} dengan ID: " . implode(', ', $metodeBelajarIds));

                                    // Tambahkan ID kompetensi yang sudah ada dan berhasil diproses ke array ini
                                    $submittedIdpKompetensiIds[] = $actualIdpKompetensiId;
                                } else {
                                    Log::warning("Upaya untuk memperbarui IdpKompetensi yang tidak ada atau tidak cocok dengan ID: {$actualIdpKompetensiId} untuk IDP: {$idp->id_idp}. Mungkin sudah dihapus atau ID salah.");
                                }
                            } catch (Exception $e) {
                                Log::error("Gagal memperbarui atau menyinkronkan kompetensi EXISTING ID {$actualIdpKompetensiId}. Error: " . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
                                throw $e; // Re-throw the exception to trigger rollback
                            }
                        } else {
                            Log::warning("Item kompetensi dengan key '{$key}' di request tidak valid (bukan 'new_' dan tidak punya 'id'). Data: ", $kompetensiData);
                        }
                    }
                    Log::debug('Selesai memproses semua item kompetensi dari request.');
                } else {
                    Log::info("Tidak ada data kompetensi yang dikirim dalam request update untuk IDP: {$idp->id_idp}.");
                }

                // 3. Logika Penghapusan Kompetensi Lama yang Tidak Terkirim
                Log::debug('Memulai logika penghapusan kompetensi lama.');
                // Ambil semua id_idpKom dari kompetensi yang saat ini terkait dengan IDP ini di database
                $currentIdpKompetensiIdsInDb = $idp->idpKompetensis()->pluck('id_idpKom')->toArray();
                Log::debug("Current IDP Kompetensi IDs in DB: " . implode(', ', $currentIdpKompetensiIdsInDb));
                Log::debug("Submitted IDP Kompetensi IDs (existing & new): " . implode(', ', $submittedIdpKompetensiIds)); // Log diubah

                // Tentukan ID yang harus dihapus (ada di DB tapi TIDAK ada di submittedIdpKompetensiIds)
                $idpKompetensiIdsToDelete = array_diff($currentIdpKompetensiIdsInDb, $submittedIdpKompetensiIds);

                if (!empty($idpKompetensiIdsToDelete)) {
                    Log::debug("Kompetensi IDP yang akan dihapus: " . implode(', ', $idpKompetensiIdsToDelete));
                    try {
                        foreach ($idpKompetensiIdsToDelete as $idToDelete) {
                            $idpKompetensiToDelete = IDPKompetensi::find($idToDelete);
                            if ($idpKompetensiToDelete) {
                                // Hapus relasi di tabel pivot 'idp_kompetensi_metode_belajars' terlebih dahulu
                                $idpKompetensiToDelete->metodeBelajars()->detach();
                                Log::debug("Metode Belajar dihapus untuk IDPKompetensi: {$idToDelete}");
                                // Kemudian hapus record IDPKompetensi itu sendiri
                                $idpKompetensiToDelete->delete();
                                Log::debug("IDPKompetensi dihapus: {$idToDelete}");
                            } else {
                                Log::warning("IDPKompetensi {$idToDelete} tidak ditemukan saat mencoba menghapus. Mungkin sudah dihapus sebelumnya.");
                            }
                        }
                        Log::debug("Penghapusan kompetensi lama selesai.");
                    } catch (Exception $e) {
                        Log::error("Gagal menghapus kompetensi lama. Error: " . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
                        throw $e; // Re-throw the exception to trigger rollback
                    }
                } else {
                    Log::info("Tidak ada kompetensi lama yang perlu dihapus untuk IDP: {$idp->id_idp}.");
                }
                Log::debug('Transaksi database akan di-commit.');
            }); // Akhir DB::transaction

            return redirect()->route('karyawan.IDP.indexKaryawan')
                ->with('success', 'Data IDP berhasil diperbarui.');
        } catch (Exception $e) {
            // DB::rollBack() sudah ditangani secara otomatis oleh DB::transaction jika terjadi Exception
            Log::error('Error saat memperbarui IDP: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
