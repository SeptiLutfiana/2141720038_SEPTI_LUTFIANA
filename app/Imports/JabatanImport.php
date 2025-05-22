<?php

namespace App\Imports;

use App\Models\Jabatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class JabatanImport implements OnEachRow, WithHeadingRow
{

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        Jabatan::create([
            'nama_jabatan' => $row['nama_jabatan'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
