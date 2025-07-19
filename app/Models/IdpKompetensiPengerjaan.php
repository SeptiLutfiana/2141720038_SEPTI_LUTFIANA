<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdpKompetensiPengerjaan extends Model
{
    protected $table = 'idp_kompetensi_pengerjaans';

    // Primary key custom
    protected $primaryKey = 'id_idpKomPeng';
     protected $fillable = [
        'id_idpKom',
        'upload_hasil',
        'keterangan_hasil',
        'status_pengerjaan',
        'saran'
    ];
    public function idpKompetensi()
    {
        return $this->belongsTo(IdpKompetensi::class, 'id_idpKom', 'id_idpKom');
    }
     public function nilaiPengerjaanIdp()
    {
        return $this->hasOne(NilaiPengerjaanIdp::class, 'id_idpKomPeng', 'id_idpKomPeng');
    }
}
