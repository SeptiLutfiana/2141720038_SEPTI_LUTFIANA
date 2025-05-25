<?php

namespace App\Exports;

use App\Models\Kompetensi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class HardKompetensiExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function array(): array
    {
        $kompetensi = Kompetensi::with(['jenjang', 'jabatan'])
            ->where('jenis_kompetensi', 'Hard Kompetensi')
            ->get();

        $data = [];
        foreach ($kompetensi as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Jenjang' => $item->jenjang?->nama_jenjang ?? '-', // Gunakan null-safe operator
                'Jabatan' => $item->jabatan?->nama_jabatan ?? '-',
                'Nama Kompetensi' => $item->nama_kompetensi,
                'Keterangan' => $item->keterangan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Jenjang', 'Jabatan', 'Nama Kompetensi', 'Keterangan'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:F1');
                $event->sheet->setCellValue('A1', 'DATA HARD KOMPETENSI INDIVIDUAL DEVELOPMENT PLAN PERHUTANI FORESTRY INSTITUTE');
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
