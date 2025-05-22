<?php

namespace App\Imports;

use App\Models\Divisi;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DivisiImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        Divisi::create([
            'nama_divisi' => $row['nama_divisi'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
