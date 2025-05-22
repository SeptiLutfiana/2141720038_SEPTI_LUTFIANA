<?php

namespace App\Imports;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use App\Models\Role;
use App\Models\Jenjang;
use App\Models\Jabatan;
use App\Models\AngkatanPSP;
use App\Models\Divisi;
use App\Models\LearingGroup;
use App\Models\Penempatan;
use App\Models\Semester;
use Illuminate\Support\Facades\Log;

class UserImport implements OnEachRow, WithHeadingRow
{ 
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Hanya proses data yang dibutuhkan, abaikan kolom 'No'
        if (!isset($data['npk']) || !isset($data['email'])) {
            return;  // Jika npk atau email kosong, abaikan baris
        }

        // Ambil ID berdasarkan nama dari Excel
        $id_role = Role::where('nama_role', $data['role'] ?? '')->value('id_role') ?? 4; // Default ID jika tidak ditemukan
        $id_jenjang = Jenjang::where('nama_jenjang', $data['jenjang'] ?? '')->value('id_jenjang') ?? 4; // Default ID jika tidak ditemukan
        $id_jabatan = Jabatan::where('nama_jabatan', $data['jabatan'] ?? '')->value('id_jabatan') ?? 4; // Default ID jika tidak ditemukan

        // Ambil ID berdasarkan bulan dan tahun untuk angkatan_psp
        $bulan = ucfirst(strtolower($data['bulan_angkatanpsp'] ?? ''));
        $tahun = $data['tahun_angkatanpsp'] ?? '';
        $id_angkatanpsp = AngkatanPSP::where('bulan', $bulan)->where('tahun', $tahun)->value('id_angkatanpsp') ?? 1;

        // Ambil ID divisi dan penempatan
        $id_divisi = Divisi::where('nama_divisi', $data['divisi'] ?? '')->value('id_divisi') ?? 1;
        $id_penempatan = Penempatan::where('nama_penempatan', $data['penempatan'] ?? '')->value('id_penempatan') ?? 1;

        // Ambil ID learning group
        $id_LG = LearingGroup::where('nama_LG', $data['learning_group'] ?? '')->value('id_LG') ?? 1;

        $id_semester = Semester::where('nama_semester', $data['semester'] ?? '')->value('id_semester') ?? 1;

        // Pastikan status valid
        $status = in_array($data['status'], ['aktif', 'verify', 'banned']) ? $data['status'] : 'aktif';

        // Menyimpan data ke dalam tabel User
       try {
        // Menyimpan data ke dalam tabel User
        $user = User::create([
            'id_role' => $id_role,
            'id_jenjang' => $id_jenjang,
            'id_jabatan' => $id_jabatan,
            'id_angkatanpsp' => $id_angkatanpsp,
            'id_divisi' => $id_divisi,
            'id_penempatan' => $id_penempatan,
            'id_LG' => $id_LG,
            'id_semester' =>$id_semester,
            'npk' => $data['npk'],
            'no_hp' => $data['no_hp'] ?? null,
            'name' => $data['name'] ?? '',
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? $data['npk']),
            'foto_profile' => null,
            'status' => $status,
        ]);

        // Menambahkan relasi role ke user yang baru dibuat
        $user->roles()->attach($id_role);

    } catch (\Exception $e) {
        Log::error('Error creating user: ' . $e->getMessage());
        return;  // Jika ada error, bisa berhenti atau tampilkan pesan error
    }
    }
}