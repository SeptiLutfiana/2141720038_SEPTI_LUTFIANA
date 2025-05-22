<?php

namespace App\Exports;

use App\Models\LearingGroup;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;


class LearningGroupExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
   public function array(): array
    {
        $learninggroup = LearingGroup::select('nama_LG', 'keterangan')->get();

        $data = [];
        foreach ($learninggroup as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Nama Learning Group' => $item->nama_LG,
                'Keterangan' => $item->keterangan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Learning Group', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:C1');
                $event->sheet->setCellValue('A1', 'DATA LEARNING GROUP INDIVIDUAL DEVELOPMENT PLAN PERHUTANI FORESTRY INSTITUTE');
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

