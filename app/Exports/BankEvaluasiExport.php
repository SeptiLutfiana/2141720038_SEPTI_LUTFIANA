<?php

namespace App\Exports;

use App\Models\BankEvaluasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class BankEvaluasiExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function array(): array
    {
        $bankEvaluasi = BankEvaluasi::select('pertanyaan', 'untuk_role', 'tipe_pertanyaan', 'jenis_evaluasi')
            ->orderByRaw("FIELD(untuk_role, 'karyawan', 'mentor', 'supervisor')")
            ->get();

        $data = [];
        foreach ($bankEvaluasi as $i => $item) {
            $data[] = [
                'No' => $i + 1,
                'Pertanyaan' => $item->pertanyaan,
                'Untuk Role' => $item->untuk_role,
                'Tipe Pertanyaan' => $item->tipe_pertanyaan,
                'Jenis Evaluasi' => $item->jenis_evaluasi,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Pertanyaan', 'Untuk Role', 'Jenis Pertanyaan', 'Jenis Evaluasi'];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menambahkan judul di atas headings
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->setCellValue('A1', 'DATA PERTANYAAN EVALUASI');
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
