<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\EvaluasiIdp;
use App\Models\BankEvaluasi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EvaluasiPascaPerRoleSheet implements FromArray, WithHeadings, WithTitle, WithEvents
{
    protected $role;
    protected $tahun;

    public function __construct($role, $tahun = null)
    {
        $this->role = $role;
        $this->tahun = $tahun;
    }

    public function array(): array
    {
        $pertanyaanLikert = BankEvaluasi::where('jenis_evaluasi', 'pasca')
            ->where('untuk_role', $this->role)
            ->where('tipe_pertanyaan', 'likert')
            ->orderBy('id_bank_evaluasi')
            ->get();

        $pertanyaanEsai = BankEvaluasi::where('jenis_evaluasi', 'pasca')
            ->where('untuk_role', $this->role)
            ->where('tipe_pertanyaan', 'esai')
            ->orderBy('id_bank_evaluasi')
            ->get();

        $pertanyaanList = $pertanyaanLikert->merge($pertanyaanEsai);


        $query = EvaluasiIdp::with(['user', 'idps', 'jawaban'])
            ->where('jenis_evaluasi', 'pasca')
            ->where('sebagai_role', $this->role);


        if ($this->tahun) {
            $query->whereYear('tanggal_evaluasi', $this->tahun);
        }

        $data = $query->get();
        $rows = [];

        foreach ($data as $index => $evaluasi) {
            $jawabanMap = $evaluasi->jawaban
                ->whereIn('id_bank_evaluasi', $pertanyaanList->pluck('id_bank_evaluasi'))
                ->keyBy('id_bank_evaluasi');

            $row = [
                $index + 1,
                $evaluasi->user->name ?? '-',
                $evaluasi->idps->proyeksi_karir ?? '-',
                \Carbon\Carbon::parse($evaluasi->tanggal_evaluasi)->format('d M Y'),
                ucfirst($evaluasi->jenis_evaluasi),
            ];

            foreach ($pertanyaanList as $p) {
                $jawaban = $jawabanMap->get($p->id_bank_evaluasi);
                if ($p->tipe_pertanyaan === 'likert') {
                    $row[] = $jawaban->jawaban_likert ?? '-';
                } elseif ($p->tipe_pertanyaan === 'esai') {
                    $row[] = $jawaban->jawaban_esai ?? '-';
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function headings(): array
    {
        $header = [
            'No',
            'Nama Pengisi',
            'Judul IDP',
            'Tanggal Evaluasi',
            'Jenis Evaluasi',
        ];
        $pertanyaanLikert = BankEvaluasi::where('jenis_evaluasi', 'pasca')
            ->where('untuk_role', $this->role)
            ->where('tipe_pertanyaan', 'likert')
            ->orderBy('id_bank_evaluasi')
            ->get();

        $pertanyaanEsai = BankEvaluasi::where('jenis_evaluasi', 'pasca')
            ->where('untuk_role', $this->role)
            ->where('tipe_pertanyaan', 'esai')
            ->orderBy('id_bank_evaluasi')
            ->get();
        $pertanyaanList = $pertanyaanLikert->merge($pertanyaanEsai);

        foreach ($pertanyaanList as $p) {
            if ($p->tipe_pertanyaan === 'likert') {
                $header[] = "{$p->pertanyaan} (Likert)";
            } elseif ($p->tipe_pertanyaan === 'esai') {
                $header[] = "{$p->pertanyaan} (Esai)";
            }
        }


        return $header;
    }
    public function title(): string
    {
        return strtoupper($this->role); // Nama sheet: KARYAWAN, MENTOR, SUPERVISOR
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Baris 1: Judul utama
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->setCellValue('A1', 'JAWABAN EVALUASI PASCA PELAKSANAAN IDP DI PERUTANI FORESTRY INSTITUTE');

                $highestColumn = $event->sheet->getDelegate()->getHighestColumn();
                $event->sheet->mergeCells("A1:{$highestColumn}1");

                // Styling judul besar
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'uppercase' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // ✅ Styling heading (judul kolom) → Baris ke-3
                $event->sheet->getStyle("A3:{$highestColumn}3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F2F2F2', // opsional: warna latar abu muda
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'AAAAAA'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
