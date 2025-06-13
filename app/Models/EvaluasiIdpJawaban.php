<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiIdpJawaban extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'evaluasi_idp_jawabans';

    // Primary key custom
    protected $primaryKey = 'id_jawaban';

    // Field yang boleh diisi massal
    protected $fillable = [
        'id_evaluasi_idp',
        'id_bank_evaluasi',
        'jawaban_likert',
        'jawaban_esai'
    ];
    public function evaluasiIdp()
    {
        return $this->belongsTo(EvaluasiIdp::class, 'id_evaluasi_idp', 'id_evaluasi_idp');
    }

    /**
     * Relasi ke model BankEvaluasi
     */
    public function bankEvaluasi()
    {
        return $this->belongsTo(BankEvaluasi::class, 'id_bank_evaluasi', 'id_bank_evaluasi');
    }
}
