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
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $jenis = $data['jenis_kompetensi'] ?? '';

        // Validasi jenis kompetensi
        if (!in_array($jenis, ['Hard Kompetensi', 'Soft Kompetensi'])) {
            return; // skip jika jenis tidak valid
        }

        // Ambil nama jenjang dan jabatan dari file
        $namaJenjang = $data['jenjang'] ?? null;
        $namaJabatan = $data['jabatan'] ?? null;

        // Cari ID berdasarkan nama, hanya untuk Hard Kompetensi
        $idJenjang = null;
        $idJabatan = null;

        if ($jenis === 'Hard Kompetensi') {
            $idJenjang = Jenjang::where('nama_jenjang', $namaJenjang)->value('id_jenjang');
            $idJabatan = Jabatan::where('nama_jabatan', $namaJabatan)->value('id_jabatan');

            // Lewatkan baris jika jenjang atau jabatan tidak ditemukan
            if (!$idJenjang || !$idJabatan) {
                return;
            }
        }

        // Simpan data
        Kompetensi::create([
            'id_jenjang'        => $idJenjang,
            'id_jabatan'        => $idJabatan,
            'nama_kompetensi'   => $data['nama_kompetensi'] ?? '',
            'jenis_kompetensi'  => $jenis,
            'keterangan'        => $data['keterangan'] ?? '',
        ]);
    }
}
