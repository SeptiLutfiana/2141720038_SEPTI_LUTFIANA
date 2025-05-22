<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\IDP;
use App\Models\Jenjang;
use App\Models\Kompetensi;
use App\Models\LearingGroup;
use App\Models\MetodeBelajar;
use App\Models\Penempatan;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IdpController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $lg = $request->query('lg');
        // $semester = $request->query('semester');

        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        // $listSemester = Semester::all();

        $idps = IDP::with(['karyawan', 'karyawan.jenjang', 'karyawan.learningGroup'])
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
        $listLG = LearingGroup::all();  // Ambil daftar learning group
        $listDivisi = Divisi::all();
        $listPenempatan = Penempatan::all();
        $kompetensis = Kompetensi::all();
        $metodeBelajars = MetodeBelajar::all();
        return view('adminsdm.BehaviorIDP.create', [
            'mentors' => $mentors,
            'supervisors' => $supervisors,
            'karyawans' => $karyawans,
            // 'semesters' => $semesters,
            'listJenjang' => $listJenjang,  // Kirimkan daftar jenjang ke view
            'listLG' => $listLG,            // Kirimkan daftar LG ke view
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

        $request->validate([
            'id_user' => $isTemplate ? 'nullable' : 'required|array',
            'id_user.*' => 'exists:users,id',
            'id_mentor' => 'nullable|exists:users,id',
            'id_supervisor' => 'required|exists:users,id',
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

        ]);

        if ($isTemplate) {
            $request->validate([
                'id_jenjang' => 'required|exists:jenjangs,id',
                'id_LG' => 'nullable|exists:learing_groups,id',
            ]);

            $query = User::where('id_jenjang', $request->id_jenjang);
            if ($request->filled('id_LG')) {
                $query->where('id_LG', $request->id_LG);
            }

            $users = $query->get();

            foreach ($users as $user) {
                $idp = IDP::create([
                    'id_user' => $user->id,
                    'id_mentor' => $request->id_mentor,
                    'id_supervisor' => $request->id_supervisor,
                    // 'id_semester' => $request->id_semester,
                    'proyeksi_karir' => $request->proyeksi_karir,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'status_approval_mentor' => 'Disetujui',
                    'status_pengajuan_idp' => 'Disetujui',
                    'status_pengerjaan' => 'Menunggu Tindakan',
                    'is_template' => false,
                    'saran_idp' => $request->saran_idp,
                    'deskripsi_idp' => $request->deskripsi_idp,
                ]);

                if ($request->has('kompetensi')) {
                    foreach ($request->kompetensi as $item) {
                        // Validasi data kompetensi
                        if (empty($item['id_kompetensi']) || empty($item['id_metode_belajar'])) {
                            continue;
                        }

                        // Gunakan transaction untuk memastikan data konsisten
                        DB::transaction(function () use ($idp, $item) {
                            // Insert ke tabel idp_kompetensis
                            $idpKompetensiId = DB::table('idp_kompetensis')->insertGetId([
                                'id_idp' => $idp->id_idp,
                                'id_kompetensi' => $item['id_kompetensi'],
                                'sasaran' => $item['sasaran'] ?? '',
                                'aksi' => $item['aksi'] ?? ''
                            ]);

                            // Insert metode belajar
                            foreach ((array)$item['id_metode_belajar'] as $idMetode) {
                                DB::table('idp_kompetensi_metode_belajars')->insert([
                                    'id_idpKom' => $idpKompetensiId,
                                    'id_metodeBelajar' => $idMetode
                                ]);
                            }
                        });
                    }
                }
            }

            return redirect()->route('admin.BehaviorIDP.index')
                ->with('msg-success', 'Berhasil membuat IDP berdasarkan jenjang dan learning group.');
        } else {
            foreach ($request->id_user as $idUser) {
                $idp = IDP::create([
                    'id_user' => $idUser,
                    'id_mentor' => $request->id_mentor,
                    'id_supervisor' => $request->id_supervisor,
                    // 'id_semester' => $request->id_semester,
                    'proyeksi_karir' => $request->proyeksi_karir,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'status_approval_mentor' => 'Disetujui',
                    'status_pengajuan_idp' => 'Disetujui',
                    'status_pengerjaan' => 'Menunggu Tindakan',
                    'is_template' => false,
                    'saran_idp' => $request->saran_idp,
                    'deskripsi_idp' => $request->deskripsi_idp,
                ]);

                if ($request->has('kompetensi')) {
                    foreach ($request->kompetensi as $item) {
                        // Validasi data kompetensi
                        if (empty($item['id_kompetensi']) || empty($item['id_metode_belajar'])) {
                            continue;
                        }

                        // Gunakan transaction untuk memastikan data konsisten
                        DB::transaction(function () use ($idp, $item) {
                            // Insert ke tabel idp_kompetensis
                            $idpKompetensiId = DB::table('idp_kompetensis')->insertGetId([
                                'id_idp' => $idp->id_idp,
                                'id_kompetensi' => $item['id_kompetensi'],
                                'sasaran' => $item['sasaran'] ?? '',
                                'aksi' => $item['aksi'] ?? ''
                            ]);

                            // Insert metode belajar
                            foreach ((array)$item['id_metode_belajar'] as $idMetode) {
                                DB::table('idp_kompetensi_metode_belajars')->insert([
                                    'id_idpKom' => $idpKompetensiId,
                                    'id_metodeBelajar' => $idMetode
                                ]);
                            }
                        });
                    }
                }
            }

            return redirect()->route('adminsdm.BehaviorIDP.index')
                ->with('msg-success', 'Berhasil membuat IDP untuk karyawan terpilih.');
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
}
