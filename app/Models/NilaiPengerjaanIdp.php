<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiPengerjaanIdp extends Model
{
     protected $table = 'nilai_pengerjaan_idps';

    // Primary key custom
    protected $primaryKey = 'id_nilaiPengerjaan';
     protected $fillable = [
        'id_idpKomPeng',
        'upload_hasil',
        'rating',
        'saran'
    ];
    public function idpKompetensiPengerjaan()
    {
        return $this->belongsTo(IdpKompetensiPengerjaan::class, 'id_idpKomPeng', 'id_idpKomPeng');
    }
}
