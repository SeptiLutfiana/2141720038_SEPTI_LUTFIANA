<?php

namespace App\Imports;

use App\Models\Semester;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SemesterImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];
    public bool $headerValid = true;

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $baris = $row->getIndex();

        // ✅ Cek header hanya sekali
        static $isHeaderChecked = false;
        if (!$isHeaderChecked) {
            $expected = ['nama_semester', 'keterangan'];
            $actual = array_keys($data);
            $missing = array_diff($expected, $actual);

            if (!empty($missing)) {
                $this->headerValid = false;
                Log::error("Header tidak sesuai. Kolom hilang: " . implode(', ', $missing));
                return;
            }

            $isHeaderChecked = true;
        }

        if (!$this->headerValid) return;

        // ❌ Kosong
        if (empty($data['nama_semester'])) {
            $this->duplikat[] = "Baris $baris: Kolom 'nama_semester' kosong.";
            return;
        }

        // ❌ Duplikat
        if (Semester::where('nama_semester', $data['nama_semester'])->exists()) {
            $this->duplikat[] = "Baris $baris: Semester '{$data['nama_semester']}' sudah ada.";
            return;
        }

        // ✅ Simpan
        Semester::create([
            'nama_semester' => $data['nama_semester'],
            'keterangan'    => $data['keterangan'] ?? '-',
        ]);

        $this->barisBerhasil++;
    }
}
