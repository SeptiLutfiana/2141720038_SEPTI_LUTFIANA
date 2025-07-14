<?php

namespace App\Imports;

use App\Models\Penempatan;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenempatanImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];
    public bool $headerValid = true;

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $baris = $row->getIndex(); // Nomor baris di Excel (1-based)

        // ✅ Cek format header hanya sekali
        static $headerChecked = false;
        if (!$headerChecked) {
            $expectedHeaders = ['nama_penempatan', 'keterangan'];
            $actualHeaders = array_keys($data);
            $missingHeaders = array_diff($expectedHeaders, $actualHeaders);

            if (!empty($missingHeaders)) {
                $this->headerValid = false;
                $this->duplikat[] = "Format header salah. Kolom yang hilang: " . implode(', ', $missingHeaders);
                return;
            }

            $headerChecked = true;
        }

        if (!$this->headerValid) return;

        // ✅ Validasi kolom wajib
        if (empty($data['nama_penempatan'])) {
            $this->duplikat[] = "Baris $baris: Kolom 'nama_penempatan' kosong.";
            return;
        }

        // ✅ Cek apakah data sudah ada di database
        $exists = Penempatan::where('nama_penempatan', $data['nama_penempatan'])->exists();
        if ($exists) {
            $this->duplikat[] = "Baris $baris: Penempatan '{$data['nama_penempatan']}' sudah ada.";
            return;
        }

        // ✅ Simpan data
        Penempatan::create([
            'nama_penempatan' => $data['nama_penempatan'],
            'keterangan' => $data['keterangan'] ?? '-',
        ]);

        $this->barisBerhasil++;
    }

    public function getFailures(): array
    {
        return $this->duplikat;
    }
}
