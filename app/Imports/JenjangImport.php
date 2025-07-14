<?php

namespace App\Imports;

use App\Models\Jenjang;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JenjangImport implements OnEachRow, WithHeadingRow
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
            $expected = ['nama_jenjang', 'keterangan'];
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

        if (empty($data['nama_jenjang'])) {
            $this->duplikat[] = "Baris $baris: Kolom 'nama_jenjang' kosong.";
            return;
        }

        if (Jenjang::where('nama_jenjang', $data['nama_jenjang'])->exists()) {
            $this->duplikat[] = "Baris $baris: Jenjang '{$data['nama_jenjang']}' sudah ada.";
            return;
        }

        Jenjang::create([
            'nama_jenjang' => $data['nama_jenjang'],
            'keterangan' => $data['keterangan'] ?? '-',
        ]);

        $this->barisBerhasil++;
    }
}
