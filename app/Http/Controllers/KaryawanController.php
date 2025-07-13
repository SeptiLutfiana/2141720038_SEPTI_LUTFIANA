<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UserImport;
use Illuminate\Support\Facades\Hash;
use App\Models\AngkatanPSP;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use App\Models\Penempatan;
use App\Models\Role;
use App\Models\Semester;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // untuk Laravel-Dompdf versi terbaru
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $lg = $request->query('lg');
        $roleUser = $request->query('role');
        $semester = $request->query('semester');
        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        $listRole = Role::all();
        $listSemester = Semester::all();
        $karyawan = User::when($search, function ($query, $search) {
            return $query->where('npk', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->where('id_jenjang', $id_jenjang);
            })
            ->when($lg, function ($query, $lg) {
                return $query->where('id_LG', $lg);
            })
            ->when($roleUser, function ($query, $roleUser) {
                return $query->where('id_role', $roleUser);
            })
            ->when($semester, function ($query, $semester) {
                return $query->where('id_semester', $semester);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.data-master.karyawan.data-karyawan.index', [
            'karyawan' => $karyawan,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listRole' => $listRole,
            'listSemester' => $listSemester,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $lg,
            'role' => $roleUser,
            'semester' => $semester,
            'type_menu' => 'data-master',
        ]);
    }


    public function create()
    {
        $roles = Role::all();
        $jenjang = Jenjang::all();
        $jabatan = Jabatan::all();
        $angkatanpsp = AngkatanPsp::all();
        $divisi = Divisi::all();
        $penempatan = Penempatan::all();
        $LG = LearingGroup::all();
        $semester = Semester::all();
        $type_menu = 'data-master';

        return view('adminsdm.data-master.karyawan.data-karyawan.create', compact(
            'roles',
            'jenjang',
            'jabatan',
            'angkatanpsp',
            'divisi',
            'penempatan',
            'LG',
            'semester',
            'type_menu'
        ));
    }

    public function store(Request $request)
    {
        // Cek apakah user menggunakan form input manual
        if ($request->filled('input_manual')) {
            // Validasi untuk input manual
            $request->validate([
                // 'id_role' => 'required|exists:roles,id_role',
                'id_jenjang' => 'required|exists:jenjangs,id_jenjang',
                'id_jabatan' => 'required|exists:jabatans,id_jabatan',
                'id_angkatanpsp' => 'required|exists:angkatan_psps,id_angkatanpsp',
                'id_divisi' => 'required|exists:divisis,id_divisi',
                'id_penempatan' => 'required|exists:penempatans,id_penempatan',
                'id_LG' => 'required|exists:learning_groups,id_LG',
                'id_semester' => 'required|exists:semesters,id_semester',
                'npk' => 'required|string|unique:users,npk',
                'no_hp' => 'nullable|string',
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'status' => 'required',
            ], [
                // 'id_role.required' => 'Role harus dipilih.',
                'id_jenjang.required' => 'Jenjang harus dipilih.',
                'id_jabatan.required' => 'Jabatan harus dipilih.',
                'id_angkatanpsp.required' => 'Angkatan PSP harus dipilih.',
                'id_divisi.required' => 'Divisi harus dipilih.',
                'id_penempatan.required' => 'Penempatan harus dipilih.',
                'id_LG.required' => 'Learning Group harus dipilih.',
                'id_semester.required' => 'Semester harus dipilih',
                'npk.required' => 'NPK harus diisi.',
                'npk.unique' => 'NPK sudah digunakan.',
                'name.required' => 'Nama Wajib Diisi',
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'password.required' => 'Password harus diisi.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);

            $user = User::create([
                'id_role' => 4,
                'id_jenjang' => $request->id_jenjang,
                'id_jabatan' => $request->id_jabatan,
                'id_angkatanpsp' => $request->id_angkatanpsp,
                'id_divisi' => $request->id_divisi,
                'id_penempatan' => $request->id_penempatan,
                'id_LG' => $request->id_LG,
                'id_semester' => $request->id_semester,
                'npk' => $request->npk,
                'no_hp' => $request->no_hp,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->npk),
                'status' => $request->status,
            ]);
            $user->roles()->attach(4);


            return redirect()->route('adminsdm.data-master.karyawan.data-karyawan.index')
                ->with('msg-success', 'Berhasil menambahkan data karyawan ' . $request->npk . ' - ' . $request->name);
        }

        // Jika user memilih upload file

        // Validasi file upload (CSV atau XLSX dengan ukuran maksimal 10MB)
        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512', // Maksimal 10MB
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
            'file_import.max' => 'Ukuran file maksimal 0.5MB.',
        ]);

        try {
            // Proses impor data dari file (gunakan paket Laravel Excel)
            $import = new UserImport;
            Excel::import($import, $request->file('file_import'));
            $berhasil = $import->baris_berhasil;
            if (count($import->failures()) > 0) {
                return redirect()->back()
                    ->with('failures', $import->failures())
                    ->with('msg-error', "$berhasil baris berhasil diimpor. " . count($import->failures()) . " baris gagal diimpor. Periksa kembali format atau duplikat NPK/Email.");
            }
            // CEK APAKAH TIDAK ADA YANG BERHASIL MASUK
            if ($import->baris_berhasil === 0) {
                return redirect()->back()
                    ->with('msg-error', 'Tidak ada data yang berhasil diimpor. Pastikan format dan isinya sudah benar.');
            }

            return redirect()->route('adminsdm.data-master.karyawan.data-karyawan.index')
                ->with('msg-success', 'Berhasil mengimpor ' . $import->baris_berhasil . ' data karyawan dari file.');
        } catch (\Exception $e) {
            // Jika ada error saat mengimpor, tangani dan tampilkan pesan error
            return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file');
        }


        // Kalau tidak dua-duanya
        // return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('adminsdm.data-master.karyawan.data-karyawan.detail', [
            'user'    => $user,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // pastikan ini diambil dari model Role
        $jenjang = Jenjang::all();
        $jabatan = Jabatan::all();
        $angkatanpsp = AngkatanPsp::all();
        $divisi = Divisi::all();
        $penempatan = Penempatan::all();
        $LG = LearingGroup::all();
        $semester = Semester::all();
        $type_menu = 'data-master';

        return view('adminsdm.data-master.karyawan.data-karyawan.edit', compact(
            'user',
            'roles',
            'jenjang',
            'jabatan',
            'angkatanpsp',
            'divisi',
            'penempatan',
            'LG',
            'semester',
            'type_menu'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->merge(['id_role' => 4]);
        $request->validate([
            'id_jenjang' => 'required|exists:jenjangs,id_jenjang',
            'id_jabatan' => 'required|exists:jabatans,id_jabatan',
            'id_angkatanpsp' => 'required|exists:angkatan_psps,id_angkatanpsp',
            'id_divisi' => 'required|exists:divisis,id_divisi',
            'id_penempatan' => 'required|exists:penempatans,id_penempatan',
            'id_LG' => 'required|exists:learning_groups,id_LG',
            'id_semester' => 'required|exists:semesters,id_semester',
            'npk' => 'required|string|unique:users,npk,' . $user->id,
            'no_hp' => 'nullable|string',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required',
        ], [
            'id_jenjang.required' => 'Jenjang harus dipilih.',
            'id_jabatan.required' => 'Jabatan harus dipilih.',
            'id_angkatanpsp.required' => 'Angkatan PSP harus dipilih.',
            'id_divisi.required' => 'Divisi harus dipilih.',
            'id_penempatan.required' => 'Penempatan harus dipilih.',
            'id_LG.required' => 'Learning Group harus dipilih.',
            'id_semester' => 'Semester harus dipilih',
            'npk.required' => 'NPK harus diisi.',
            'npk.unique' => 'NPK sudah digunakan.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $data = $request->except(['password', 'foto_profile']);

        // Jika password diisi, hash dan simpan
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Jika ada file foto_profile diupload
        if ($request->hasFile('foto_profile')) {
            $file = $request->file('foto_profile');
            $path = $file->store('profile_photos', 'public');
            $data['foto_profile'] = $path;
        }
        // Update relasi roles (jika ada perubahan role)
        $user->roles()->sync([$request->id_role]);
        $user->update($data);

        return redirect()->route('adminsdm.data-master.karyawan.data-karyawan.index')
            ->with('msg-success', 'Berhasil mengubah data Karyawan ' . $user->npk .  "-" . $user->name);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('adminsdm.data-master.karyawan.data-karyawan.index')->with('msg-success', 'Berhasil menghapus data  ' . $user->npk .  "-" . $user->name);
    }
    public function switchRole(Request $request)
    {
        // Validasi bahwa role yang diminta benar
        $role = $request->input('role');
        $allowedRoles = [1, 2, 3, 4]; // Admin, Supervisor, Mentor, Karyawan

        if (!in_array($role, $allowedRoles)) {
            return redirect()->back()->with('error', 'Invalid role');
        }

        // Menyimpan role baru dalam session
        session(['role' => $role]);

        // Kembalikan ke halaman sebelumnya
        return redirect()->back()->with('success', 'Role switched successfully');
    }
    public function printPdf()
    {
        $user = User::all(); // Atau filter sesuai kebutuhan
        $waktuCetak = Carbon::now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.data-karyawan.karyawan_pdf', [
            'user' => $user,
            'type_menu' => 'data-master',
            'waktuCetak' => $waktuCetak,
        ])->setPaper('a4', 'landscape');;
        return $pdf->stream('data-karyawan.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new UserExport, 'data-karyawan.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new UserExport, 'data-karyawan.csv');
    }
    public function exportDocx()
    {
        $users = User::with([
            'jenjang',
            'learningGroup',
            'jabatan',
            'penempatan',
            'divisi',
            'semester',
            'angkatanpsp',
            'roles',
        ])->get();

        $phpWord = new PhpWord();

        // Tambahkan section dengan orientasi landscape
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
        ]);

        // Tambahkan logo di kiri atas
        $section->addImage(public_path('img/logo-perhutani.png'), [
            'width' => 100,
            'height' => 100,
            'wrappingStyle' => 'square',
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
        ]);

        $section->addTextBreak(1);

        // Judul
        $section->addText('Data User Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Buat tabel
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);

        // Header tabel
        $table->addRow();
        $headers = [
            'No',
            'NPK',
            'Nama',
            'Email',
            'No HP',
            'Jenjang',
            'Learning Group',
            'Jabatan',
            'Penempatan',
            'Divisi',
            'Semester',
            'Jenjang (lagi)',
            'Bulan Angkatan PSP',
            'Tahun Angkatan PSP',
            'Role',
            'Status'
        ];
        foreach ($headers as $header) {
            $table->addCell(1500)->addText($header, ['bold' => true], ['alignment' => 'center']);
        }

        // Data rows
        foreach ($users as $i => $user) {
            $table->addRow();
            $table->addCell(1500)->addText($i + 1);
            $table->addCell(1500)->addText($user->npk ?? '-');
            $table->addCell(1500)->addText($user->name ?? '-');
            $table->addCell(1500)->addText($user->email ?? '-');
            $table->addCell(1500)->addText($user->no_hp ?? '-');
            $table->addCell(1500)->addText($user->jenjang->nama_jenjang ?? '-');
            $table->addCell(1500)->addText($user->learningGroup->nama_LG ?? '-');
            $table->addCell(1500)->addText($user->jabatan->nama_jabatan ?? '-');
            $table->addCell(1500)->addText($user->penempatan->nama_penempatan ?? '-');
            $table->addCell(1500)->addText($user->divisi->nama_divisi ?? '-');
            $table->addCell(1500)->addText($user->semester->nama_semester ?? '-');
            $table->addCell(1500)->addText($user->jenjang->nama_jenjang ?? '-');  // kolom "Jenjang" lagi
            $table->addCell(1500)->addText($user->angkatanpsp->bulan ?? '-');
            $table->addCell(1500)->addText($user->angkatanpsp->tahun ?? '-');
            $table->addCell(1500)->addText($user->roles->pluck('nama_role')->implode(', ') ?? '-');
            $table->addCell(1500)->addText($user->status ?? '-');
        }

        $fileName = 'data-user.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
    public function getJabatanByJenjang($id)
    {
        $jabatan = Jabatan::where('id_jenjang', $id)->get();

        return response()->json($jabatan);
    }
}
