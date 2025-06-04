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

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        return view('supervisor.spv-dashboard', [
            'type_menu' => 'supervisor',
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
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|in:1,2,3,4,5',
            'saran' => 'nullable|string',
            'id_idpKomPeng' => 'required|exists:idp_kompetensi_pengerjaans,id_idpKomPeng',
        ]);

        NilaiPengerjaanIdp::updateOrCreate(
            ['id_idpKomPeng' => $request->id_idpKomPeng], // condition (jika sudah ada)
            [ // update data
                'rating' => $request->rating,
                'saran' => $request->saran,
            ]
        );

        return redirect()->back()->with('msg-success', 'Penilaian berhasil disimpan.');
    }
}
