<?php

namespace App\Exports;

use App\Models\Penempatan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PenempatanExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
   public function array(): array
    {
        $penempatan = Penempatan::select('nama_penempatan', 'keterangan')->get();

        $data = [];
        foreach ($penempatan as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Nama Penempatan' => $item->nama_penempatan,
                'Keterangan' => $item->keterangan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Penempatan', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'DATA PENEMPATAN KARYAWAN PERHUTANI FORESTRY INSTITUTE');
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
