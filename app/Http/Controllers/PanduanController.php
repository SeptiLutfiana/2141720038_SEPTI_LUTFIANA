<?php

namespace App\Http\Controllers;

use App\Models\Panduan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PanduanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $datas = Panduan::with('roles')->get();
        $panduan = Panduan::when($search, function ($query, $search) {
            return $query->where('judul', 'like', "%$search%");
        })
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.Panduan.index', [
            'type_menu' => 'idps',
            'panduan' => $panduan,
            'search' => $search,
            'datas' => $datas,
        ]);
    }
    public function create()
    {
        $panduan = Panduan::all();
        $role = Role::all(); // <-- Pastikan ini ada
        return view('adminsdm.Panduan.cretae', [
            'type_menu' => 'idps',
            'panduan' => $panduan,
            'role' => $role,

        ]);
    }
    public function store(Request $request)
    {

        $request->validate([
            'judul' => 'required|string',
            'isi' => 'required|string',
            'id_role' => 'required|exists:roles,id_role', // validasi jenjang

        ], [
            'judul.required' => 'Judul harus diisi',
            'isi.required' => 'Isi harus diisi',
            'id_role.required' => 'Role harus dipilih',
            'id_role.exists' => 'Role tidak valid',
        ]);

        $panduan = Panduan::create([
            'judul' => $request->judul,
            'isi' => $request->isi,

        ]);
        // Simpan ke tabel pivot
        \App\Models\PanduanRole::create([
            'id_panduan' => $panduan->id_panduan,
            'id_role' => $request->id_role,
        ]);

        return redirect()->route('adminsdm.Panduan.index')
            ->with('msg-success', 'Berhasil menambahkan data Panduan Baru ' . $request->judul);
    }
    public function show($id)
    {
        // Mengambil data Jabatan berdasarkan ID
        $panduan = Panduan::with('roles')->findOrFail($id);

        return view('adminsdm.Panduan.detail', [
            'panduan'    => $panduan,
            'type_menu' => 'idps',
        ]);
    }
    public function edit($id)
    {
        $panduan = Panduan::findOrFail($id);
        $role = Role::all(); // <-- Pastikan ini ada
        return view('adminsdm.Panduan.edit', [
            'panduan'    => $panduan,
            'role' => $role,
            'type_menu' => 'idps',
        ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string',
            'isi' => 'required|string',
            'id_role' => 'required|exists:roles,id_role',
        ], [
            'judul.required' => 'Judul harus diisi',
            'isi.required' => 'Isi harus diisi',
            'id_role.required' => 'Role harus dipilih',
            'id_role.exists' => 'Role tidak valid',
        ]);

        $panduan = \App\Models\Panduan::findOrFail($id);

        // Update data panduan
        $panduan->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
        ]);

        // Update relasi role di tabel pivot
        \App\Models\PanduanRole::updateOrCreate(
            ['id_panduan' => $panduan->id_panduan],
            ['id_role' => $request->id_role]
        );

        return redirect()->route('adminsdm.Panduan.index')
            ->with('msg-success', 'Berhasil memperbarui data Panduan: ' . $request->judul);
    }

    public function destroy(Panduan $panduan)
    {
        $panduan->delete();
        return redirect()->route('adminsdm.Panduan.index')->with('msg-success', 'Berhasil menghapus data  ' . $panduan->judul);
    }
    public function autoShowPanduanKaryawan()
    {
        $user = Auth::user();

        // Pastikan user punya role karyawan
        $isKaryawan = $user->roles->contains('id_role', 4);

        if (!$isKaryawan) {
            abort(403, 'Anda tidak memiliki akses ke panduan ini.');
        }

        // Ambil panduan pertama yang ditujukan untuk role karyawan
        $panduan = Panduan::whereHas('roles', function ($query) {
            $query->where('nama_role', 'karyawan');
        })->first();

        if (!$panduan) {
            return view('karyawan.Panduan.kosong'); // bisa isi dengan "Panduan belum tersedia"
        }

        return view('karyawan.Panduan.detail', [
            'panduan' => $panduan,
            'type_menu' => 'karyawan',
        ]);
    }
    public function autoShowPanduanMentor()
    {
        $user = Auth::user();

        // Pastikan user punya role karyawan
        $isKaryawan = $user->roles->contains('id_role', 3);

        if (!$isKaryawan) {
            abort(403, 'Anda tidak memiliki akses ke panduan ini.');
        }

        // Ambil panduan pertama yang ditujukan untuk role karyawan
        $panduan = Panduan::whereHas('roles', function ($query) {
            $query->where('nama_role', 'mentor');
        })->first();
        return view('mentor.Panduan.index', [
            'panduan' => $panduan,
            'type_menu' => 'mentor',
        ]);
    }
    public function autoShowPanduanSupervisor()
    {
        $user = Auth::user();

        // Pastikan user punya role karyawan
        $isKaryawan = $user->roles->contains('id_role', 2);

        if (!$isKaryawan) {
            abort(403, 'Anda tidak memiliki akses ke panduan ini.');
        }

        // Ambil panduan pertama yang ditujukan untuk role karyawan
        $panduan = Panduan::whereHas('roles', function ($query) {
            $query->where('nama_role', 'supervisor');
        })->first();
        return view('supervisor.Panduan.index', [
            'panduan' => $panduan,
            'type_menu' => 'supervisor',
        ]);
    }
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $path = $request->file('file')->store('implementasi', 'public');
        $url = Storage::url($path); // Hasil: /storage/uploads/files/nama_file.pdf

        return $url;
    }
}
