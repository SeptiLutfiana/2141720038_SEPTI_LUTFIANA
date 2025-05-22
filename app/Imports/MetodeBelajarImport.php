<?php

namespace App\Imports;

use App\Models\MetodeBelajar;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class MetodeBelajarImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        MetodeBelajar::create([
            'nama_metodeBelajar' => $row['nama_metodebelajar'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
