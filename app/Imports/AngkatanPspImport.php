<?php

namespace App\Imports;

use App\Models\AngkatanPSP;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AngkatanPspImport implements OnEachRow, WithHeadingRow
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
            $expected = ['bulan', 'tahun'];
            $actual = array_keys($data);
            $missing = array_diff($expected, $actual);

            if (!empty($missing)) {
                $this->headerValid = false;
                Log::error("❌ Format header tidak sesuai. Kolom hilang: " . implode(', ', $missing));
                return;
            }

            $isHeaderChecked = true;
        }

        // ❌ Lewatkan jika header tidak valid
        if (!$this->headerValid) {
            return;
        }

        // ✅ Cek kosong
        if (empty($data['bulan']) || empty($data['tahun'])) {
            $this->duplikat[] = "Baris $baris: Kolom 'bulan' atau 'tahun' kosong.";
            return;
        }

        // ✅ Validasi bulan
        $daftarBulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        if (!in_array($data['bulan'], $daftarBulan)) {
            $this->duplikat[] = "Baris $baris: Bulan '{$data['bulan']}' tidak valid.";
            return;
        }

        // ✅ Cek duplikat di database
        $exists = AngkatanPSP::where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->exists();

        if ($exists) {
            $this->duplikat[] = "Baris $baris: Data untuk bulan {$data['bulan']} tahun {$data['tahun']} sudah ada.";
            return;
        }

        // ✅ Simpan data
        AngkatanPSP::create([
            'bulan' => $data['bulan'],
            'tahun' => $data['tahun'],
        ]);

        $this->barisBerhasil++;
    }
}
