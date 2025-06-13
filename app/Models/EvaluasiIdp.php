<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiIdp extends Model
{

    use HasFactory;

    // Nama tabelnya
    protected $table = 'evaluasi_idps';

    // Primary key custom
    protected $primaryKey = 'id_evaluasi_idp';

    // Field yang boleh diisi massal
    protected $fillable = [
        'id_idp',
        'id_user',
        'jenis_evaluasi',
        'tanggal_evaluasi',
        'sebagai_role',
    ];
    public function idps()
    {
        return $this->belongsTo(IDP::class, 'id_idp', 'id_idp');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
    public function jawaban()
    {
        return $this->hasMany(EvaluasiIdpJawaban::class, 'id_evaluasi_idp');
    }
}
