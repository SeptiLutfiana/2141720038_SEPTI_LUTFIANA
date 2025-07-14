<?php

namespace App\Imports;

use App\Models\Divisi;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DivisiImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];
    public bool $headerValid = true;

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $baris = $row->getIndex();

        // ✅ Cek header hanya satu kali
        static $isHeaderChecked = false;
        if (!$isHeaderChecked) {
            $expected = ['nama_divisi', 'keterangan'];
            $actual = array_keys($data);
            $missing = array_diff($expected, $actual);

            if (!empty($missing)) {
                $this->headerValid = false;
                Log::error("❌ Format header tidak sesuai. Kolom yang hilang: " . implode(', ', $missing));
                return;
            }

            $isHeaderChecked = true;
        }

        // ❌ Lewatkan jika header invalid
        if (!$this->headerValid) {
            return;
        }

        // ✅ Validasi isi wajib
        if (empty($data['nama_divisi'])) {
            $this->duplikat[] = "Baris $baris: Kolom 'nama_divisi' kosong.";
            return;
        }

        // ✅ Cek apakah data sudah ada
        $exists = Divisi::where('nama_divisi', $data['nama_divisi'])->exists();

        if ($exists) {
            $this->duplikat[] = "Baris $baris: Divisi '{$data['nama_divisi']}' sudah ada.";
            return;
        }

        // ✅ Simpan jika valid
        Divisi::create([
            'nama_divisi' => $data['nama_divisi'],
            'keterangan'  => $data['keterangan'] ?? '-',
        ]);

        $this->barisBerhasil++;
    }
}
