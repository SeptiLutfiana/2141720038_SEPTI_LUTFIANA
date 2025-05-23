<?php

namespace App\Exports;

use App\Models\Jabatan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class JabatanExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function array(): array
    {
        $jabatan = Jabatan::with('jenjang')->get();

        $data = [];
        foreach ($jabatan as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Nama Jabatan' => $item->nama_jabatan,
                'Jenjang' => $item->jenjang ? $item->jenjang->nama_jenjang : '-',  // Tambah kolom jenjang
                'Keterangan' => $item->keterangan,

            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Jabatan','Jenjang', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->setCellValue('A1', 'DATA JABATAN');
                // Styling judul
                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
