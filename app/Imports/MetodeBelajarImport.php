<?php

namespace App\Imports;

use App\Models\MetodeBelajar;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MetodeBelajarImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];
    public bool $headerValid = true;

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $baris = $row->getIndex();

        // Cek header sekali
        static $isHeaderChecked = false;
        if (!$isHeaderChecked) {
            $expected = ['nama_metodebelajar', 'keterangan'];
            $actual = array_keys($data);
            $missing = array_diff($expected, $actual);
            if (!empty($missing)) {
                $this->headerValid = false;
                $this->duplikat[] = "Kolom header tidak sesuai. Kolom yang hilang: " . implode(', ', $missing);
                return;
            }
            $isHeaderChecked = true;
        }

        if (!$this->headerValid) return;

        $nama = trim($data['nama_metodebelajar'] ?? '');
        $keterangan = trim($data['keterangan'] ?? '');

        if (empty($nama)) {
            $this->duplikat[] = "Baris $baris: Nama metode belajar kosong.";
            return;
        }

        $exists = MetodeBelajar::where('nama_metodeBelajar', $nama)->exists();
        if ($exists) {
            $this->duplikat[] = "Baris $baris: Metode belajar '$nama' sudah ada.";
            return;
        }

        MetodeBelajar::create([
            'nama_metodeBelajar' => $nama,
            'keterangan' => $keterangan,
        ]);

        $this->barisBerhasil++;
    }
}
