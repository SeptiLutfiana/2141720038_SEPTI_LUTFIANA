<?php

namespace App\Http\Controllers;

use App\Models\AngkatanPSP;
use App\Models\Divisi;
use App\Models\IDP;
use App\Models\IdpKompetensi;
use App\Models\Jabatan;
use App\Models\Jenjang;
use App\Models\Kompetensi;
use App\Models\LearingGroup;
use App\Models\MetodeBelajar;
use App\Models\Penempatan;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use FFI\Exception;
use App\Notifications\GivenIDPNotification;
use App\Notifications\IDPBaruDibuatNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use App\Models\IdpRekomendasi;
class IdpController extends Controller
{
    public function indexGiven(Request $request)
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
            ->with(['karyawan', 'karyawan.jenjang', 'karyawan.learningGroup'])
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

        return view('adminsdm.BehaviorIDP.index', [
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
    public function indexBankIdp(Request $request)
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
        $idps = IDP::query(); // Mulai dengan query dasar
        $idps->where('is_template', true)
            ->with(['karyawan', 'karyawan.jenjang', 'karyawan.learningGroup'])
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
                    $q->where('id_LG', $id_LG);
                });
            })
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('waktu_mulai', $tahun);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.BehaviorIDP.ListIDP.index', [
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
        return view('adminsdm.BehaviorIDP.create', [
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
            'type_menu' => 'idps'
        ]);
    }

    public function store(Request $request)
    {
        $isTemplate = !$request->has('id_user') || empty($request->id_user);

        $commonRules = [
            // id_user nullable jika isTemplate true, required|array jika false
            'id_user' => $isTemplate ? 'nullable' : 'required|array',
            'id_user.*' => 'exists:users,id',
            'id_mentor' => 'nullable|exists:users,id',
            'proyeksi_karir' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'status_approval_mentor' => 'nullable|in:Menunggu Persetujuan,Disetujui,Ditolak',
            'status_pengajuan_idp' => 'nullable|in:Menunggu Persetujuan,Revisi,Disetujui,Tidak Disetujui',
            'status_pengerjaan' => 'nullable|in:Menunggu Tindakan,Revisi,Sedang Dikerjakan,Selesai',
            'saran_idp' => 'nullable|string',
            'deskripsi_idp' => 'nullable|string',
            'kompetensi' => 'required|array',
            'kompetensi.*.id_kompetensi' => 'required|exists:kompetensis,id_kompetensi',
            'kompetensi.*.id_metode_belajar' => 'required|array|min:1',
            'kompetensi.*.id_metode_belajar.*' => 'exists:metode_belajars,id_metodeBelajar',
            'kompetensi.*.sasaran' => 'required|string',
            'kompetensi.*.aksi' => 'required|string',
            'kompetensi.*.peran' => 'required|in:umum,utama,kunci_core,kunci_bisnis,kunci_enabler',
        ];

        if ($isTemplate) {
            $templateRules = [
                'id_supervisor_jenjang' => 'required|exists:users,id',
                'id_jenjang_bank' => 'required|exists:jenjangs,id_jenjang',
                'id_LG' => 'nullable|exists:users,id_LG',
                'max_applies' => 'required|integer|min:0',
            ];
            $request->validate(array_merge($commonRules, $templateRules));

            // Ambil nama jenjang dan LG untuk pesan sukses
            $namaJenjang = Jenjang::find($request->id_jenjang_bank)->nama_jenjang ?? 'Tidak Diketahui';
            $namaLG = optional(LearingGroup::find($request->id_LG))->nama_LG ?? 'Tidak Diketahui';


            // --- PERUBAHAN UTAMA UNTUK BANK IDP ---
            // Hanya buat SATU IDP sebagai template
            $idp = null;
            DB::transaction(function () use ($request, $namaJenjang, $namaLG, $idp) {
                $idp = IDP::create([
                    'id_user' => null,
                    'id_mentor' => null,
                    'id_supervisor' => $request->id_supervisor_jenjang,
                    'proyeksi_karir' => $request->proyeksi_karir,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'status_approval_mentor' => 'Menunggu Persetujuan',
                    'status_pengajuan_idp' => 'Menunggu Persetujuan',
                    'status_pengerjaan' => 'Menunggu Tindakan', // Status awal template
                    'is_template' => true, // Ini HARUS true untuk Bank IDP
                    'saran_idp' => $request->saran_idp,
                    'deskripsi_idp' => $request->deskripsi_idp,
                    'id_jenjang' => $request->id_jenjang_bank, // Simpan jenjang untuk template
                    'id_LG' => $request->id_LG, // Simpan LG untuk template
                    'max_applies' => $request->max_applies,

                ]);
                $supervisor = User::find($request->id_supervisor_jenjang);
                if ($supervisor && $idp) {
                    $supervisor->notify(new IDPBaruDibuatNotification([
                        'id_idp' => $idp->id_idp,
                        'nama_karyawan' => 'Admin SDM',
                        'peran' => 'Supervisor',
                        'untuk_role' => 'supervisor',
                        'link' => route('supervisor.IDP.showSupervisor', $idp->id_idp),
                    ]));
                }
                foreach ($request->kompetensi as $item) {
                    if (empty($item['id_kompetensi']) || empty($item['id_metode_belajar'])) {
                        continue;
                    }

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
            // --- AKHIR PERUBAHAN UTAMA UNTUK BANK IDP ---
            return redirect()->route('adminsdm.BehaviorIDP.ListIDP.indexBankIdp')
                ->with('msg-success', 'Berhasil membuat IDP Bank untuk jenjang ' . $namaJenjang . ' dan learning group ' . ($namaLG ? $namaLG : 'Semua Learning Group'));
        } else { // Ini adalah Given IDP
            $customRules = [
                'id_supervisor' => 'required|exists:users,id',
            ];
            $request->validate(array_merge($commonRules, $customRules));

            $userIds = $request->id_user;
            $users = User::whereIn('id', $userIds)->get();
            $userNames = $users->pluck('name')->implode(', '); // Get names and join them with a comma

            $createdIdps = [];

            DB::transaction(function () use ($userIds, $request, &$createdIdps) {
                foreach ($userIds as $idUser) {
                    $idp = $this->createIdpForUser($idUser, $request, false);
                    $createdIdps[$idUser] = $idp;
                }
            });

            foreach ($users as $user) {
                if (isset($createdIdps[$user->id])) {
                    $user->notify(new GivenIDPNotification($createdIdps[$user->id]->id_idp, 'karyawan'));
                    // Notifikasi ke mentor (jika dipilih)
                    if ($request->id_mentor) {
                        $mentor = User::find($request->id_mentor);
                        if ($mentor) {
                            $mentor->notify(new IDPBaruDibuatNotification([
                                'id_idp' => $createdIdps[$user->id]->id_idp,
                                'nama_karyawan' => $user->name,
                                'peran' => 'Mentor',
                                'untuk_role' => 'mentor',
                                'link' => route('mentor.IDP.mentor.idp.show', $createdIdps[$user->id]->id_idp),
                            ]));
                        }
                    }

                    // Notifikasi ke supervisor
                    $supervisor = User::find($request->id_supervisor);
                    if ($supervisor) {
                        $supervisor->notify(new IDPBaruDibuatNotification([
                            'id_idp' => $createdIdps[$user->id]->id_idp,
                            'nama_karyawan' => $user->name,
                            'peran' => 'supervisor',
                            'untuk_role' => 'supervisor',
                            'link' => route('supervisor.IDP.showSupervisor', $createdIdps[$user->id]->id_idp),
                        ]));
                    }
                }
            }
            return redirect()->route('adminsdm.BehaviorIDP.indexGiven')
                ->with('msg-success', 'Berhasil membuat IDP untuk karyawan terpilih: ' . $userNames);
        }
    }

    private function createIdpForUser($userId, Request $request, $isTemplate = false)
    {
        $user = User::findOrFail($userId);
        $idp = IDP::create([
            'id_user' => $userId,
            'id_mentor' => $request->id_mentor,
            // Perbaikan di sini: gunakan id_supervisor_jenjang untuk template
            'id_supervisor' => $isTemplate ? $request->id_supervisor_jenjang : $request->id_supervisor,
            'proyeksi_karir' => $request->proyeksi_karir,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status_approval_mentor' => 'Disetujui',
            'status_pengajuan_idp' => 'Disetujui',
            'status_pengerjaan' => 'Menunggu Tindakan',
            'is_template' => $isTemplate,
            'saran_idp' => $request->saran_idp,
            'deskripsi_idp' => $request->deskripsi_idp,

            // mengambil data berdsarkan nama karyawan yg dipilih
            'id_jenjang' => $user->jenjang->id_jenjang,
            'id_jabatan' => $user->jabatan->id_jabatan,
            'id_LG' => $user->learningGroup->id_LG,
            'id_divisi' => $user->divisi->id_divisi,
            'id_penempatan' => $user->penempatan->id_penempatan,
            'id_semester' => $user->semester->id_semester,
            'id_angkatanpsp' => $user->angkatanPsp->id_angkatanpsp
        ]);

        foreach ($request->kompetensi as $item) {
            if (empty($item['id_kompetensi']) || empty($item['id_metode_belajar']) || empty($item['sasaran']) || empty($item['aksi'])) {
                continue;
            }


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
        return $idp;
    }

    public function showGiven($id)
    {
        // Mengambil data Divisi berdasarkan ID
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars' // relasi kompetensi beserta metode belajar
        ])->findOrFail($id);
        return view('adminsdm.BehaviorIDP.detail', [
            'idps'    => $idps,
            'type_menu' => 'idps',
        ]);
    }
    public function showBank($id)
    {
        // Mengambil data Divisi berdasarkan ID
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars' // relasi kompetensi beserta metode belajar
        ])->findOrFail($id);
        return view('adminsdm.BehaviorIDP.ListIDP.detail', [
            'idps'    => $idps,
            'type_menu' => 'idps',
        ]);
    }
    public function indexSupervisor()
    {
        $supervisorId = Auth::id();

        $idps = IDP::with([
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars'
        ])
            ->where('id_supervisor', $supervisorId)  // pastikan pakai 'id_mentor' sesuai kolom di DB
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('supervisor.IDP.index', [
            'idps' => $idps,
            'type_menu' => 'idps',
        ]);
    }
    public function getJabatanByJenjang($id)
    {
        $jabatan = Jabatan::where('id_jenjang', $id)->get();
        return response()->json($jabatan);
    }

    public function getKompetensiByJabatan(Request $request, $id) // Ubah parameter menjadi Request $request, $id
    {
        // Ambil ID kompetensi yang sudah ada dari request AJAX
        // Ini akan berisi ID dari Hard maupun Soft Kompetensi yang sudah ada di IDP
        $existingKompetensiIds = $request->input('existing_kompetensi_ids', []);
        Log::debug('Existing Kompetensi IDs dari request (untuk filter Hard Kompetensi):', $existingKompetensiIds);

        // Query kompetensi:
        // 1. Berdasarkan id_jabatan
        // 2. Hanya untuk 'Hard Kompetensi' (sesuai klarifikasi Anda)
        $query = Kompetensi::where('id_jabatan', $id)
            ->where('jenis_kompetensi', 'Hard Kompetensi'); // Tetap pertahankan filter ini

        // 3. Tambahkan kondisi untuk mengecualikan kompetensi yang sudah ada
        if (!empty($existingKompetensiIds)) {
            $query->whereNotIn('id_kompetensi', $existingKompetensiIds);
            Log::debug('Mengecualikan Hard Kompetensi dengan ID:', $existingKompetensiIds);
        }

        $kompetensi = $query->get(['id_kompetensi', 'nama_kompetensi', 'jenis_kompetensi']);

        Log::debug('Hard Kompetensi yang ditemukan:', $kompetensi->toArray());

        return response()->json($kompetensi);
    }
    public function editGiven($id)
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

        return view('adminsdm.BehaviorIDP.edit', [
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
            'type_menu' => 'idps'
        ]);
    }
    public function updateGiven(Request $request, $id)
    {
        $idp = IDP::findOrFail($id);
        Log::debug('Data request yang diterima:', $request->all());

        $validated = $request->validate([
            'proyeksi_karir' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
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
                $idp->update([
                    'proyeksi_karir' => $validated['proyeksi_karir'],
                    'waktu_mulai' => $validated['waktu_mulai'],
                    'waktu_selesai' => $validated['waktu_selesai'],
                    'deskripsi_idp' => $validated['deskripsi_idp'] ?? null,
                    'id_mentor' => $validated['id_mentor'],
                    'id_supervisor' => $validated['id_supervisor'],
                ]);
                Log::debug("IDP utama dengan ID {$idp->id_idp} berhasil diperbarui.");

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

            return redirect()->route('adminsdm.BehaviorIDP.indexGiven')
                ->with('msg-success', 'Data IDP berhasil diperbarui.');
        } catch (Exception $e) {
            // DB::rollBack() sudah ditangani secara otomatis oleh DB::transaction jika terjadi Exception
            Log::error('Error saat memperbarui IDP: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    public function editBank($id)
    {
        $idp = IDP::with(['idpKompetensis.metodeBelajars'])->findOrFail($id);

        // Ambil data yang dibutuhkan untuk form
        $supervisors = User::whereHas('roles', fn($q) => $q->where('user_roles.id_role', 2))->get();
        $karyawans = User::whereHas('roles', fn($q) => $q->where('user_roles.id_role', 4))->get();
        $listJenjang = Jenjang::all();
        $listJabatan = Jabatan::all();
        $listLG = LearingGroup::all();
        $listDivisi = Divisi::all();
        $listPenempatan = Penempatan::all();
        $kompetensi = Kompetensi::all();
        $metodeBelajars = MetodeBelajar::all();

        return view('adminsdm.BehaviorIDP.ListIDP.edit', [
            'idp' => $idp,
            'supervisors' => $supervisors,
            'karyawans' => $karyawans,
            'listJabatan' => $listJabatan,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listDivisi' => $listDivisi,
            'listPenempatan' => $listPenempatan,
            'kompetensi' => $kompetensi,
            'metodeBelajars' => $metodeBelajars,
            'type_menu' => 'idps'
        ]);
    }
    public function updateBank(Request $request, $id)
    {
        $idp = IDP::findOrFail($id);
        Log::debug('Data request yang diterima:', $request->all());

        $validated = $request->validate([
            'proyeksi_karir' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'deskripsi_idp' => 'nullable|string',
            'id_supervisor' => 'required|exists:users,id',
            'max_applies' => 'required|integer',
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
                $idp->update([
                    'proyeksi_karir' => $validated['proyeksi_karir'],
                    'waktu_mulai' => $validated['waktu_mulai'],
                    'waktu_selesai' => $validated['waktu_selesai'],
                    'deskripsi_idp' => $validated['deskripsi_idp'] ?? null,
                    'max_applies' => $validated['max_applies'],
                    'id_supervisor' => $validated['id_supervisor'],
                ]);
                Log::debug("IDP utama dengan ID {$idp->id_idp} berhasil diperbarui.");

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

            return redirect()->route('adminsdm.BehaviorIDP.ListIDP.indexBankIdp')
                ->with('msg-success', 'Data IDP berhasil diperbarui.');
        } catch (Exception $e) {
            // DB::rollBack() sudah ditangani secara otomatis oleh DB::transaction jika terjadi Exception
            Log::error('Error saat memperbarui IDP: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    public function destroyGiven(IDP $idp)
    {
        $idp->delete();
        return redirect()->route('adminsdm.BehaviorIDP.indexGiven')->with('msg-success', 'Berhasil menghapus data Idp ' . $idp->proyeksi_karir);
    }
    public function destroyBank(IDP $idp)
    {
        $idp->delete();
        return redirect()->route('adminsdm.BehaviorIDP.ListIDP.indexBankIdp')->with('msg-success', 'Berhasil menghapus data Idp ' . $idp->proyeksi_karir);
    }
    public function cetakPDF()
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
        ])
            ->where('is_template', true)
            ->get();
        // Waktu cetak
        $waktuCetak = Carbon::now()->translatedFormat('d F Y H:i');
        $pdf = Pdf::loadView('adminsdm.BehaviorIDP.ListIDP.bankidp_pdf', [
            'idps' => $idps,
            'waktuCetak' => $waktuCetak,
            'type_menu' => 'idps'
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Bank-IDP.pdf');
    }
    // Di IDPController.php atau controller lain
    public function getSoftKompetensi(Request $request)
    {
        $existingKompetensiIds = $request->input('existing_kompetensi_ids', []);

        $query = Kompetensi::where('jenis_kompetensi', 'Soft Kompetensi');

        if (!empty($existingKompetensiIds)) {
            $query->whereNotIn('id_kompetensi', $existingKompetensiIds);
        }

        $kompetensi = $query->get(['id_kompetensi', 'nama_kompetensi']);

        return response()->json($kompetensi);
    }
    public static function autoGenerateRekomendasi()
    {
        $now = now();

        $idps = IDP::with('idpKompetensis.pengerjaans')
            ->where('waktu_selesai', '<', $now)
            ->doesntHave('rekomendasis')
            ->where('is_template', '!=', 1) // tidak hitung template
            ->get();

        foreach ($idps as $idp) {
            $belumDikerjakan = $idp->idpKompetensis->filter(function ($komp) {
                return $komp->pengerjaans->isEmpty();
            });

            if ($belumDikerjakan->isEmpty() && $idp->status_pengerjaan === 'Disetujui Mentor') {
                continue;
            }

            if ($belumDikerjakan->isNotEmpty()) {
                IdpRekomendasi::create([
                    'id_idp' => $idp->id_idp,
                    'hasil_rekomendasi' => 'Tidak Disarankan',
                    'deskripsi_rekomendasi' => 'IDP tidak disarankan karena tidak semua kompetensi dikerjakan hingga batas waktu.',
                    'nilai_akhir_soft' => null,
                    'nilai_akhir_hard' => null,
                ]);
            }
        }
    }
}
