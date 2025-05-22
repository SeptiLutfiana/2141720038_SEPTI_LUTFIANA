<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UserExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    public function array(): array
    {
        $users = User::with([
            'jenjang', 
            'learningGroup', 
            'jabatan', 
            'penempatan', 
            'divisi', 
            'semester', 
            'angkatanpsp', 
            'roles'
        ])->get();

        $data = [];
        foreach ($users as $i => $user) {
            $data[] = [
                'No' => $i + 1,
                'NPK' => $user->npk ?? '-',
                'Name' => $user->name,
                'Email' => $user->email,
                'No HP' => $user->no_hp ?? '-',
                'Jenjang' => $user->jenjang->nama_jenjang ?? '-',
                'Learning Group' => $user->learningGroup->nama_LG ?? '-',
                'Jabatan' => $user->jabatan->nama_jabatan ?? '-',
                'Penempatan' => $user->penempatan->nama_penempatan ?? '-',
                'Divisi' => $user->divisi->nama_divisi ?? '-',
                'Semester' => $user->semester->nama_semester ?? '-',
                'Bulan Angkatan PSP' => $user->angkatanpsp->bulan ?? '-',
                'Tahun Angkatan PSP' => $user->angkatanpsp->tahun ?? '-',
                'Roles' => $user->roles->pluck('nama_role')->implode(', '),
                'Status' => $user->status ?? '-',
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No', 'NPK', 'Name', 'Email', 'No HP', 'Jenjang', 'Learning Group', 
            'Jabatan', 'Penempatan', 'Divisi', 'Semester', 
            'Bulan Angkatan PSP', 'Tahun Angkatan PSP', 'Roles', 'Status'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:P1'); // 16 kolom
                $event->sheet->setCellValue('A1', 'DATA KARYAWAN');

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
