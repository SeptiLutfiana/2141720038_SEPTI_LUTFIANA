<?php

namespace App\Imports;

use App\Models\Kompetensi;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KompetensiImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $jenis = $data['jenis_kompetensi'] ?? '';
        if (!in_array($jenis, ['Hard Kompetensi', 'Soft Kompetensi'])) {
            $jenis = null; // Atau 'Hard Kompetensi' sebagai default jika diinginkan
            }
            Kompetensi::create([
                'nama_kompetensi'   => $data['nama_kompetensi'] ?? '',
                'jenis_kompetensi'  => $jenis,
                'keterangan'        => $data['keterangan'] ?? '',
    ]);
    }
}
