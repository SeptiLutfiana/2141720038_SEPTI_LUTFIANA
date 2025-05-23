<?php

namespace App\Imports;

use App\Models\Jabatan;
use App\Models\Jenjang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class JabatanImport implements OnEachRow, WithHeadingRow
{

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $id_jenjang = Jenjang::where('nama_jenjang', $row['jenjang'] ?? '')->value('id_jenjang') ?? 4;

        // Ambil data, abaikan kolom 'no'
        Jabatan::create([
            'nama_jabatan' => $row['nama_jabatan'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
            'id_jenjang'   => $id_jenjang

        ]);
    }
}
