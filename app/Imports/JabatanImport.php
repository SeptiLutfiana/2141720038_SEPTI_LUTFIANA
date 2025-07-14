<?php

namespace App\Imports;

use App\Models\Jabatan;
use App\Models\Jenjang;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Exception;

class JabatanImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $baris = $row->getIndex(); // Ambil nomor baris dari file

        if (!isset($data['jenjang']) || !isset($data['nama_jabatan'])) {
            throw new Exception("Format header salah. Pastikan kolom 'jenjang' dan 'nama_jabatan' ada.");
        }

        $id_jenjang = Jenjang::where('nama_jenjang', $data['jenjang'])->value('id_jenjang');

        if (!$id_jenjang) {
            throw new Exception("Data jenjang '{$data['jenjang']}' tidak ditemukan di database.");
        }

        // Cek duplikat
        $jabatanExist = Jabatan::where('nama_jabatan', $data['nama_jabatan'])
            ->where('id_jenjang', $id_jenjang)
            ->exists();

        if ($jabatanExist) {
            $this->duplikat[] = "Baris $baris: Jabatan '{$data['nama_jabatan']}' dengan jenjang '{$data['jenjang']}' sudah ada di database.";
            return;
        }

        Jabatan::create([
            'nama_jabatan' => $data['nama_jabatan'],
            'keterangan'   => $data['keterangan'] ?? '',
            'id_jenjang'   => $id_jenjang,
        ]);

        $this->barisBerhasil++;
    }
}
