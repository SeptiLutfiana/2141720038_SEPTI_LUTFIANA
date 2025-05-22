<?php

namespace App\Imports;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Semester;

class SemesterImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        Semester::create([
            'nama_semester' => $row['nama_semester'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
