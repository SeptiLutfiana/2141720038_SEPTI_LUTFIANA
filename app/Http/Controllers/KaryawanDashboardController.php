<?php

namespace App\Http\Controllers;

use App\Models\IDP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use App\Models\IdpKompetensiPengerjaan;
use Illuminate\Support\Facades\Log;

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
            'idpKompetensis.metodeBelajars'
        ])
            ->where('id_user', $user->id) // Ambil IDP hanya milik user login
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
    public function showKaryawan($id)
    {
        // Mengambil data Divisi berdasarkan ID
        $idps = IDP::with([
            'karyawan',      // relasi banyak karyawan jika ada
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars', // relasi kompetensi beserta metode belajar
            'idpKompetensis.pengerjaans'
        ])->findOrFail($id);
        return view('karyawan.IDP.detail', [
            'idps'    => $idps,
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

        return redirect()->back()->with('success', 'File dan data berhasil disimpan.');
    }
}
