<?php

namespace App\Imports;

use App\Models\LearingGroup;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LearningGroupImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];
    public bool $headerValid = true;

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $baris = $row->getIndex();

        // Cek header hanya sekali
        static $isHeaderChecked = false;
        if (!$isHeaderChecked) {
            $expected = ['nama_lg', 'keterangan'];
            $actual = array_keys($data);
            $missing = array_diff($expected, $actual);

            if (!empty($missing)) {
                $this->headerValid = false;
                Log::error("Header tidak sesuai. Kolom hilang: " . implode(', ', $missing));
                return;
            }

            $isHeaderChecked = true;
        }

        if (!$this->headerValid) {
            return;
        }

        // Validasi kolom kosong
        if (empty($data['nama_lg'])) {
            $this->duplikat[] = "Baris $baris: Kolom 'nama_lg' kosong.";
            return;
        }

        // Cek duplikat
        if (LearingGroup::where('nama_LG', $data['nama_lg'])->exists()) {
            $this->duplikat[] = "Baris $baris: Learning Group '{$data['nama_lg']}' sudah ada.";
            return;
        }

        LearingGroup::create([
            'nama_LG' => $data['nama_lg'],
            'keterangan' => $data['keterangan'] ?? '-',
        ]);

        $this->barisBerhasil++;
    }
}
