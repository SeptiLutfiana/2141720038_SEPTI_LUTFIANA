<?php

namespace App\Imports;

use App\Models\AngkatanPSP;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AngkatanPspImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        AngkatanPSP::create([
            'bulan' => $row['bulan'] ?? '',
            'tahun'  => $row['tahun'] ?? '',
        ]);
    }
}
