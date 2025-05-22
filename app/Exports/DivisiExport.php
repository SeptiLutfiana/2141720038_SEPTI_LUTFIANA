<?php

namespace App\Exports;

use App\Models\Divisi;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class DivisiExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{public function array(): array
    {
        $divisi = Divisi::select('nama_divisi', 'keterangan')->get();

        $data = [];
        foreach ($divisi as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Nama Divisi' => $item->nama_divisi,
                'Keterangan' => $item->keterangan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Divisi', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->setCellValue('A1', 'DATA DIVISI');
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
