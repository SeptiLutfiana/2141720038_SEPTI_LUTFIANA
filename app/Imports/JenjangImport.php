<?php

namespace App\Imports;

use App\Models\Jenjang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class JenjangImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        Jenjang::create([
            'nama_jenjang' => $row['nama_jenjang'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
