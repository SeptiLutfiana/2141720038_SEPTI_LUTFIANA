<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
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
use Maatwebsite\Excel\Concerns\SkipsFailures;

class UserImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $baris_berhasil = 0;

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Cek header wajib (sekali saja)
        static $isHeaderChecked = false;
        if (!$isHeaderChecked) {
            $expected = [
                'no', 'npk', 'name', 'email', 'no_hp', 'password',
                'role', 'jenjang', 'learning_group', 'semester',
                'jabatan', 'divisi', 'penempatan',
                'bulan_angkatanpsp', 'tahun_angkatanpsp', 'status'
            ];

            $actual = array_keys($data);
            $missing = array_diff($expected, $actual);

            if (!empty($missing)) {
                Log::error('❌ Format header tidak sesuai. Kolom hilang: ' . implode(', ', $missing));
                return;
            }

            $isHeaderChecked = true;
        }

        // Skip jika data dasar kosong
        if (!isset($data['npk']) || !isset($data['email'])) {
            return;
        }

        // ✅ Cek apakah NPK atau Email sudah ada
        $npkExists = User::where('npk', $data['npk'])->exists();
        $emailExists = User::where('email', $data['email'])->exists();

        if ($npkExists || $emailExists) {
            $msg = 'Data dengan ';
            if ($npkExists) $msg .= 'NPK "' . $data['npk'] . '" ';
            if ($emailExists) {
                $msg .= $npkExists ? 'dan ' : '';
                $msg .= 'email "' . $data['email'] . '" ';
            }
            $msg .= 'sudah ada di database.';

            $this->failures[] = new Failure(
                $row->getIndex(),
                'npk/email',
                [$msg],
                $row->toArray()
            );
            return;
        }

        // Ambil ID Referensi
        $id_role = Role::where('nama_role', $data['role'] ?? '')->value('id_role') ?? 4;
        $id_jenjang = Jenjang::where('nama_jenjang', $data['jenjang'] ?? '')->value('id_jenjang') ?? 4;
        $id_jabatan = Jabatan::where('nama_jabatan', $data['jabatan'] ?? '')->value('id_jabatan') ?? 4;

        $bulan = ucfirst(strtolower($data['bulan_angkatanpsp'] ?? ''));
        $tahun = $data['tahun_angkatanpsp'] ?? '';
        $id_angkatanpsp = AngkatanPSP::where('bulan', $bulan)->where('tahun', $tahun)->value('id_angkatanpsp') ?? 1;

        $id_divisi = Divisi::where('nama_divisi', $data['divisi'] ?? '')->value('id_divisi') ?? 1;
        $id_penempatan = Penempatan::where('nama_penempatan', $data['penempatan'] ?? '')->value('id_penempatan') ?? 1;
        $id_LG = LearingGroup::where('nama_LG', $data['learning_group'] ?? '')->value('id_LG') ?? 1;
        $id_semester = Semester::where('nama_semester', $data['semester'] ?? '')->value('id_semester') ?? 1;

        $status = in_array(strtolower($data['status']), ['aktif', 'verify', 'banned']) ? strtolower($data['status']) : 'aktif';

        try {
            $user = User::create([
                'id_role' => $id_role,
                'id_jenjang' => $id_jenjang,
                'id_jabatan' => $id_jabatan,
                'id_angkatanpsp' => $id_angkatanpsp,
                'id_divisi' => $id_divisi,
                'id_penempatan' => $id_penempatan,
                'id_LG' => $id_LG,
                'id_semester' => $id_semester,
                'npk' => $data['npk'],
                'no_hp' => $data['no_hp'] ?? null,
                'name' => $data['name'] ?? '',
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? $data['npk']),
                'foto_profile' => null,
                'status' => $status,
            ]);

            $user->roles()->attach($id_role);
            $this->baris_berhasil++;
        } catch (\Exception $e) {
            Log::error('❌ Gagal menambahkan user NPK: ' . ($data['npk'] ?? '-') . ' | Error: ' . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            '*.npk' => ['required'],
            '*.name' => ['required'],
            '*.email' => ['required', 'email'],
            '*.password' => ['required'],
            '*.role' => ['required'],
            '*.jenjang' => ['required'],
            '*.learning_group' => ['required'],
            '*.semester' => ['required'],
            '*.jabatan' => ['required'],
            '*.divisi' => ['required'],
            '*.penempatan' => ['required'],
            '*.bulan_angkatanpsp' => ['required'],
            '*.tahun_angkatanpsp' => ['required'],
            '*.status' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.npk.required' => 'Kolom NPK wajib diisi.',
            '*.name.required' => 'Kolom Nama wajib diisi.',
            '*.email.required' => 'Kolom Email wajib diisi.',
            '*.email.email' => 'Format Email tidak valid.',
            '*.password.required' => 'Kolom Password wajib diisi.',
            '*.role.required' => 'Kolom Role wajib diisi.',
            '*.jenjang.required' => 'Kolom Jenjang wajib diisi.',
            '*.learning_group.required' => 'Kolom Learning Group wajib diisi.',
            '*.semester.required' => 'Kolom Semester wajib diisi.',
            '*.jabatan.required' => 'Kolom Jabatan wajib diisi.',
            '*.divisi.required' => 'Kolom Divisi wajib diisi.',
            '*.penempatan.required' => 'Kolom Penempatan wajib diisi.',
            '*.bulan_angkatanpsp.required' => 'Kolom Bulan Angkatan PSP wajib diisi.',
            '*.tahun_angkatanpsp.required' => 'Kolom Tahun Angkatan PSP wajib diisi.',
            '*.status.required' => 'Kolom Status wajib diisi.',
        ];
    }

    public function failures()
    {
        return $this->failures;
    }
}
