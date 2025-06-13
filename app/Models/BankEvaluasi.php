<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankEvaluasi extends Model
{

    use HasFactory;

    // Nama tabelnya
    protected $table = 'bank_evaluasis';

    // Primary key custom
    protected $primaryKey = 'id_bank_evaluasi';

    // Field yang boleh diisi massal
    protected $fillable = [
        'jenis_evaluasi',
        'untuk_role',
        'tipe_pertanyaan',
        'pertanyaan'
    ];
    public function evaluasiIdpJawabans()
    {
        return $this->hasMany(EvaluasiIdpJawaban::class, 'id_bank_evaluasi');
    }
}
