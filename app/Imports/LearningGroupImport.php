<?php

namespace App\Imports;

use App\Models\LearingGroup;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class LearningGroupImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        LearingGroup::create([
            'nama_LG' => $row['nama_lg'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
