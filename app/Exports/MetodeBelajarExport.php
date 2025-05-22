<?php

namespace App\Exports;

use App\Models\MetodeBelajar;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MetodeBelajarExport implements FromArray, WithHeadings,  WithEvents, ShouldAutoSize
{
    public function array(): array
    {
        $metodebelajar = MetodeBelajar::select('nama_metodeBelajar', 'keterangan')->get();

        $data = [];
        foreach ($metodebelajar as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Metode Belajar' => $item->nama_metodeBelajar,
                'Keterangan' => $item->keterangan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Metode Belajar', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:C1');
                $event->sheet->setCellValue('A1', 'DATA METODE BELAJAR INDIVIDUAL DEVELOPMENT PLAN PERHUTANI FORESTRY INSTITUTE');
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
