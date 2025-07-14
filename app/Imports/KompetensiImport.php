<?php

namespace App\Imports;

use App\Models\Kompetensi;
use App\Models\Jenjang;
use App\Models\Jabatan;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KompetensiImport implements OnEachRow, WithHeadingRow
{
    public int $barisBerhasil = 0;
    public array $duplikat = [];
    public bool $headerValid = true;

    public function onRow(Row $row)
    {
        $baris = $row->getIndex();
        $data = $row->toArray();

        static $isHeaderChecked = false;
        if (!$isHeaderChecked) {
            $expected = ['nama_kompetensi', 'jenis_kompetensi', 'keterangan'];
            $missing = array_diff($expected, array_keys($data));
            if (!empty($missing)) {
                $this->headerValid = false;
                $this->duplikat[] = "❌ Header tidak sesuai. Kolom hilang: " . implode(', ', $missing);
                return;
            }
            $isHeaderChecked = true;
        }

        if (!$this->headerValid) return;

        $jenis = ucwords(strtolower(trim($data['jenis_kompetensi'] ?? '')));
        if (!in_array($jenis, ['Hard Kompetensi', 'Soft Kompetensi'])) {
            $this->duplikat[] = "Baris $baris: Jenis kompetensi tidak valid.";
            return;
        }

        $idJenjang = null;
        $idJabatan = null;

        if ($jenis === 'Hard Kompetensi') {
            $namaJenjang = trim($data['jenjang'] ?? '');
            $namaJabatan = trim($data['jabatan'] ?? '');

            $idJenjang = Jenjang::where('nama_jenjang', $namaJenjang)->value('id_jenjang');
            $idJabatan = Jabatan::where('nama_jabatan', $namaJabatan)->value('id_jabatan');

            if (!$idJenjang || !$idJabatan) {
                $this->duplikat[] = "Baris $baris: Jenjang atau Jabatan tidak ditemukan.";
                return;
            }
        }

        $nama = trim($data['nama_kompetensi'] ?? '');
        $keterangan = trim($data['keterangan'] ?? '');

        if (empty($nama)) {
            $this->duplikat[] = "Baris $baris: Nama kompetensi kosong.";
            return;
        }

        $query = Kompetensi::where('nama_kompetensi', $nama)
            ->where('jenis_kompetensi', $jenis);

        if ($jenis === 'Hard Kompetensi') {
            $query->where('id_jenjang', $idJenjang)
                ->where('id_jabatan', $idJabatan);
        }

        if ($query->exists()) {
            $this->duplikat[] = "Baris $baris: Data kompetensi duplikat.";
            return;
        }

        Kompetensi::create([
            'id_jenjang'        => $idJenjang,
            'id_jabatan'        => $idJabatan,
            'nama_kompetensi'   => $nama,
            'jenis_kompetensi'  => $jenis,
            'keterangan'        => $keterangan,
        ]);

        $this->barisBerhasil++;
    }
}
