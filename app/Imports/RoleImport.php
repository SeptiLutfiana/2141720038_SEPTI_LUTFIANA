<?php

namespace App\Imports;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
class RoleImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Ambil data, abaikan kolom 'no'
        Role::create([
            'nama_role' => $row['nama_role'] ?? '',
            'keterangan'  => $row['keterangan'] ?? '',
        ]);
    }
}
