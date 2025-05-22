<?php

namespace App\Exports;

use App\Models\AngkatanPSP;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class AngkatanPspExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
   public function array(): array
    {
        $angkatanpsp = AngkatanPSP::select('bulan', 'tahun')->get();

        $data = [];
        foreach ($angkatanpsp as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Bulan' => $item->bulan,
                'Tahun' => $item->tahun,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Bulan Angkatan PSP', 'Tahun Angkatan PSP'];
    }
     public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->setCellValue('A1', 'DATA ANGKATAN PSP KARYAWAN');
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
