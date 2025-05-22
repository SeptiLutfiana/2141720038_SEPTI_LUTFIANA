<?php

namespace App\Http\Controllers;

use App\Imports\RoleImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
    
        $role = Role::when($search, function ($query, $search) {
                        return $query->where('nama_role', 'like', "%$search%")
                                     ->orWhere('keterangan', 'like', "%$search%");
                    })
                    ->orderBy('nama_role')
                    ->paginate(10)
                    ->withQueryString(); // agar ?search=... tetap terbawa saat paging
    
        return view('adminsdm.data-master.karyawan.role.index', [
            'type_menu' => 'role',
            'role' => $role,
            'search' => $search,
        ]);
    }
    public function create()
    {
      return view('adminsdm.data-master.karyawan.role.create', [
          'type_menu' => 'tambah-role',
      ]);
    }
    public function store(Request $request)
  {
      // Cek apakah user menggunakan form input manual
      if ($request->filled('input_manual')) {
          // Validasi untuk input manual
          $request->validate([
              'nama_role' => 'required|string',
              'keterangan' => 'required|string',
          ], [
              'nama_role.required' => 'Nama Role harus diisi',
              'keterangan.required' => 'Keterangan harus diisi',
          ]);
  
          Role::create([
              'nama_role' => $request->nama_role,
              'keterangan' => $request->keterangan,
          ]);
  
          return redirect()->route('adminsdm.data-master.karyawan.role.index')
              ->with('msg-success', 'Berhasil menambahkan data role ' . $request->nama_role);
      }
  
     // Jika user memilih upload file
     if ($request->hasFile('file_import')) {
      // Validasi file upload (CSV atau XLSX dengan ukuran maksimal 10MB)
      $request->validate([
          'file_import' => 'required|mimes:xlsx,csv|max:10240', // Maksimal 10MB
      ], [
          'file_import.required' => 'File harus diupload.',
          'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
          'file_import.max' => 'Ukuran file maksimal 10MB.',
      ]);
  
      try {
          // Proses impor data dari file (gunakan paket Laravel Excel)
          Excel::import(new RoleImport, $request->file('file_import'));
  
          // Redirect ke halaman Data dengan pesan sukses
          return redirect()->route('adminsdm.data-master.karyawan.role.index')
              ->with('msg-success', 'Berhasil mengimpor data role dari file.');
      } catch (\Exception $e) {
          // Jika ada error saat mengimpor, tangani dan tampilkan pesan error
          return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
      }
  }
  
      // Kalau tidak dua-duanya
      return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
  }
  
    public function show($id)
    {
        $role = Role::findOrFail($id);
    
        return view('adminsdm.data-master.karyawan.role.detail', [
            'role'    => $role,
            'type_menu' => 'role', 
        ]);
    }  
    public function edit($id)
  {
      $role = Role::findOrFail($id);
      return view('adminsdm.data-master.karyawan.role.edit', [
          'role'    => $role,
          'type_menu' => 'role', 
      ]);
  }
  
  public function update(Request $request, $id)
  {
      $request->validate([
          'nama_role' => 'required|string',
          'keterangan' => 'required|string',
      ], [
          'nama_role.required' => 'Nama Role harus diisi',
      ]);
  
      $role = Role::findOrFail($id);
      
      $role->update($request->all());
  
      return redirect()->route('adminsdm.data-master.karyawan.role.index')
          ->with('msg-success', 'Berhasil mengubah data Role ' . $role->nama_role);
  }
  
    
    public function destroy(Role $role)
    {
      $role->delete();
      return redirect()->route('adminsdm.data-master.karyawan.role.index')->with('msg-success', 'Berhasil menghapus data role ' . $role->nama_role);
    }
}
