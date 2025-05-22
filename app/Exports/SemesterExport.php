<?php

namespace App\Exports;

use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SemesterExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function array(): array
    {
        $semester = Semester::select('nama_semester', 'keterangan')->get();

        $data = [];
        foreach ($semester as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Nama Semester' => $item->nama_semester,
                'Keterangan' => $item->keterangan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Semester', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'DAFTAR SEMESTER INDIVIDUAL DEVELOPMENT PLAN PERHUTANI FORESTRY INSTITUTE');
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
