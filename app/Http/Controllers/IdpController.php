<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\IDP;
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
            'type_menu' => 'idp'
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
            'kompetensi.*.id_metode_belajar' => 'required|array',
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
            $namaLG = '';
            if ($request->filled('id_LG')) {
                $namaLG = LearingGroup::find($request->id_LG)->nama_LG ?? 'Tidak Diketahui';
            }

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
}
