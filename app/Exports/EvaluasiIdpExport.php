<?php

namespace App\Exports;

use App\Models\EvaluasiIdp;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EvaluasiIdpExport implements WithMultipleSheets
{
    protected $tahun;

    public function __construct($tahun = null)
    {
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        return [
            new EvaluasiPascaPerRoleSheet('karyawan', $this->tahun),
            new EvaluasiPascaPerRoleSheet('mentor', $this->tahun),
            new EvaluasiPascaPerRoleSheet('supervisor', $this->tahun),
        ];
    }
}
