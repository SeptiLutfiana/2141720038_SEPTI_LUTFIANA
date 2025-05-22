<?php

namespace App\Exports;

use App\Models\UserRole;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class MentorExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function array(): array
    {
        $mentor = UserRole::with('user')
            ->where('id_role', 3)
            ->get();

        $data = [];
        $no = 1;
        foreach ($mentor as $item) {
            $data[] = [
                $no++,
                $item->user->name,
                $item->user->npk,
                $item->user->no_hp,
                $item->user->jabatan->nama_jabatan,
                $item->user->penempatan->nama_penempatan,
                $item->user->divisi->nama_divisi,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'NPK', 'No HP', 'Jabatan', 'Penempatan', 'Divisi'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'DAFTAR MENTOR INDIVIDUAL DEVELOPMENT PLAN PERHUTANI FORESTRY INSTITUTE');
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
