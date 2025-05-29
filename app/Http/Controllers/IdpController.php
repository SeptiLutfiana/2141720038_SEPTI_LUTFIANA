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

class IdpController extends Controller
{
    public function indexGiven(Request $request)
    {
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $lg = $request->query('lg');
        // $semester = $request->query('semester');

        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        // $listSemester = Semester::all();
        $idps = IDP::query(); // Mulai dengan query dasar
        $idps->where('is_template', false)
            ->with(['karyawan', 'karyawan.jenjang', 'karyawan.learningGroup'])
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
            ->when($lg, function ($query, $lg) {
                return $query->whereHas('karyawan', function ($q) use ($lg) {
                    $q->where('lg', $lg);
                });
            })
            // ->when($semester, function ($query, $semester) {
            //     return $query->where('id_semester', $semester);
            // })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.BehaviorIDP.index', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            // 'listSemester' => $listSemester,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $lg,
            // 'semester' => $semester,
            'type_menu' => 'idps',
        ]);
    }
    public function indexBankIdp(Request $request)
    {
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $lg = $request->query('lg');
        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        $idps = IDP::query(); // Mulai dengan query dasar
        $idps->where('is_template', true)
            ->with(['karyawan', 'karyawan.jenjang', 'karyawan.learningGroup'])
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
            ->when($lg, function ($query, $lg) {
                return $query->whereHas('karyawan', function ($q) use ($lg) {
                    $q->where('lg', $lg);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.BehaviorIDP.ListIDP.index', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $lg,
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
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
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
        ];

        if ($isTemplate) {
            $templateRules = [
                'id_supervisor_jenjang' => 'required|exists:users,id',
                'id_jenjang_bank' => 'required|exists:jenjangs,id_jenjang',
                'id_LG' => 'nullable|exists:users,id_LG',
            ];
            $request->validate(array_merge($commonRules, $templateRules));

            // Ambil nama jenjang dan LG untuk pesan sukses
            $namaJenjang = Jenjang::find($request->id_jenjang_bank)->nama_jenjang ?? 'Tidak Diketahui';
            $namaLG = optional(LearingGroup::find($request->id_LG))->nama_LG ?? 'Tidak Diketahui';


            // --- PERUBAHAN UTAMA UNTUK BANK IDP ---
            // Hanya buat SATU IDP sebagai template
            DB::transaction(function () use ($request, $namaJenjang, $namaLG) {
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
                ]);

                foreach ($request->kompetensi as $item) {
                    if (empty($item['id_kompetensi']) || empty($item['id_metode_belajar'])) {
                        continue;
                    }

                    $idpKompetensiId = DB::table('idp_kompetensis')->insertGetId([
                        'id_idp' => $idp->id_idp,
                        'id_kompetensi' => $item['id_kompetensi'],
                        'sasaran' => $item['sasaran'],
                        'aksi' => $item['aksi'],
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

            return redirect()->route('adminsdm.BehaviorIDP.indexBankIdp')
                ->with('msg-success', 'Berhasil membuat IDP Bank untuk jenjang ' . $namaJenjang . ' dan learning group ' . ($namaLG ? $namaLG : 'Semua Learning Group'));
        } else { // Ini adalah Given IDP
            $customRules = [
                'id_supervisor' => 'required|exists:users,id',
            ];
            $request->validate(array_merge($commonRules, $customRules));

            $userIds = $request->id_user;
            $users = User::whereIn('id', $userIds)->get();
            $userNames = $users->pluck('name')->implode(', '); // Get names and join them with a comma

            foreach ($request->id_user as $idUser) {
                DB::transaction(function () use ($idUser, $request) {
                    $this->createIdpForUser($idUser, $request, false); // untuk Given IDP
                });
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
            'is_template' => $isTemplate, // <-- PERBAIKAN PENTING: Set ini sesuai parameter $isTemplate
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
            ]);

            foreach ($item['id_metode_belajar'] as $idMetode) {
                DB::table('idp_kompetensi_metode_belajars')->insert([
                    'id_idpKom' => $idpKompetensiId,
                    'id_metodeBelajar' => $idMetode,
                ]);
            }
        }
    }

    public function show($id)
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
    public function indexKaryawan()
    {
        $user = Auth::user(); // Ambil user yang sedang login

        $idps = IDP::with([
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars'
        ])
            ->where('id_user', $user->id) // Ambil IDP hanya milik user login
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('karyawan.idp.index', [
            'idps' => $idps,
            'type_menu' => 'idps',
        ]);
    }
    public function showKaryawan($id)
    {
        // Mengambil data Divisi berdasarkan ID
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars' // relasi kompetensi beserta metode belajar
        ])->findOrFail($id);
        return view('karyawan.IDP.detail', [
            'idps'    => $idps,
            'type_menu' => 'idps',
        ]);
    }
    public function indexMentor()
    {
        $mentorId = Auth::id();

        $idps = IDP::with([
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars'
        ])
            ->where('id_mentor', $mentorId)  // pastikan pakai 'id_mentor' sesuai kolom di DB
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('mentor.IDP.index', [
            'idps' => $idps,
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

    public function getKompetensiByJabatan($id)
    {
        $kompetensi = Kompetensi::where('id_jabatan', $id)->where('jenis_kompetensi', 'Hard Kompetensi')->get();
        return response()->json($kompetensi);
    }
    public function edit($id)
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

    public function update(Request $request, $id)
    {
        $idp = IDP::findOrFail($id);

        $validated = $request->validate([
            'proyeksi_karir' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'deskripsi_idp' => 'nullable|string',
            'id_mentor' => 'required|exists:users,id',
            'id_supervisor' => 'required|exists:users,id',
            'kompetensi' => 'nullable|array',
            'kompetensi.*.id' => 'required|integer|exists:idp_kompetensis,id_idpKom', // Pastikan ini adalah primary key
            'kompetensi.*.sasaran' => 'required|string',
            'kompetensi.*.aksi' => 'required|string',
            'kompetensi.*.id_metode_belajar' => 'nullable|array',
            'kompetensi.*.id_metode_belajar.*' => 'integer|exists:idp_kompetensi_metode_belajars,id_metodeBelajar',
        ]);

        try {
            DB::transaction(function () use ($idp, $validated) {
                // Update data IDP utama
                $idp->update([
                    'proyeksi_karir' => $validated['proyeksi_karir'],
                    'waktu_mulai' => $validated['waktu_mulai'],
                    'waktu_selesai' => $validated['waktu_selesai'],
                    'deskripsi_idp' => $validated['deskripsi_idp'] ?? null,
                    'id_mentor' => $validated['id_mentor'],
                    'id_supervisor' => $validated['id_supervisor'],
                ]);

                Log::debug("IDP utama dengan ID {$idp->id_idp} berhasil diperbarui.");

                // Update kompetensi jika ada
                if (!empty($validated['kompetensi']) && is_array($validated['kompetensi'])) {
                    foreach ($validated['kompetensi'] as $kompetensiData) {
                        $actualIdpKompetensiId = $kompetensiData['id'];

                        // Lebih baik menggunakan find() jika id_idpKom adalah primary key
                        $idpKompetensi = IdpKompetensi::find($actualIdpKompetensiId);

                        Log::debug("Memproses kompetensi ID: {$actualIdpKompetensiId}");
                        Log::debug("Ditemukan IdpKompetensi: " . ($idpKompetensi ? $idpKompetensi->id_idpKom : 'Tidak ditemukan'));
                        Log::debug("IDP ID saat ini: {$idp->id_idp}");
                        Log::debug("IdpKompetensi's IDP ID: " . ($idpKompetensi ? $idpKompetensi->id_idp : 'N/A'));

                        // Memastikan kompetensi yang akan diupdate adalah milik IDP ini
                        if ($idpKompetensi && $idpKompetensi->id_idp == $idp->id_idp) {
                            Log::debug("Kondisi terpenuhi untuk ID: {$actualIdpKompetensiId}. Mencoba update.");
                            $idpKompetensi->update([
                                'sasaran' => $kompetensiData['sasaran'],
                                'aksi' => $kompetensiData['aksi'],
                            ]);
                            Log::debug("Sasaran diperbarui menjadi: {$kompetensiData['sasaran']}");
                            Log::debug("Aksi diperbarui menjadi: {$kompetensiData['aksi']}");

                            $metodeBelajarIds = $kompetensiData['id_metode_belajar'] ?? [];
                            $idpKompetensi->metodeBelajars()->sync($metodeBelajarIds);
                            Log::debug("Metode Belajar disinkronkan dengan ID: " . implode(', ', $metodeBelajarIds));
                        } else {
                            Log::warning("Upaya untuk memperbarui IdpKompetensi yang tidak ada atau tidak cocok dengan ID: {$actualIdpKompetensiId} untuk IDP: {$idp->id_idp}");
                        }
                    }
                } else {
                    Log::info("Tidak ada data kompetensi yang dikirim untuk diperbarui.");
                }
            });

            return redirect()->route('adminsdm.BehaviorIDP.indexGiven')
                ->with('msg-success', 'Data IDP berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating IDP: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()
                ->withInput()
                ->with('msg-error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
