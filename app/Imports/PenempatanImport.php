<?php

namespace App\Imports;

use App\Models\Penempatan;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class PenempatanImport implements OnEachRow, WithHeadingRow{

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        Penempatan::create([
            'nama_penempatan' => $row['nama_penempatan'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
