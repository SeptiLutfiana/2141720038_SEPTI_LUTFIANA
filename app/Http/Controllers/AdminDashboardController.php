<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\IDP;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('adminsdm.dashboard', [
            'type_menu' => 'dashboard',
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
        $pdf = Pdf::loadView('adminsdm.BehaviorIDP.RiwayatIDP.riwayat_pdf', [
            'idps' => $idps,
            'type_menu' => 'data-master',
            'waktuCetak' => $waktuCetak,
        ]);

        return $pdf->stream('Data-IDP-Terfilter.pdf');
    }
}
