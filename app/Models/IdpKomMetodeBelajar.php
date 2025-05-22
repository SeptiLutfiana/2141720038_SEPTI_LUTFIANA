<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdpKomMetodeBelajar extends Model
{
      // Nama tabelnya
    protected $table = 'idp_kompetensi_metode_belajars';

    // Primary key custom
    protected $primaryKey = 'id_komMetode';
     protected $fillable = [
        'id_idpKom',
        'id_metodeBelajar',
    ];
    
    // Relasi ke IDP Kompetensi
    public function idpKompetensi()
    {
        return $this->belongsTo(IdpKompetensi::class, 'id_idpKom');
    }

    // Relasi ke Metode Belajar
    public function metodeBelajar()
    {
        return $this->belongsTo(MetodeBelajar::class, 'id_metodeBelajar');
    }
}
