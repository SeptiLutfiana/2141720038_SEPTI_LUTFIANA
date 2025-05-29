<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdpKompetensi extends Model
{
    // Nama tabelnya
    protected $table = 'idp_kompetensis';

    // Primary key custom
    protected $primaryKey = 'id_idpKom';
    public $timestamps = false;
    protected $fillable = [
        'id_idp',
        'id_kompetensi',
        'sasaran',
        'aksi',
    ];
    public function idp()
    {
        return $this->belongsTo(IDP::class, 'id_idp');
    }
    public function kompetensi()
    {
        return $this->belongsTo(Kompetensi::class, 'id_kompetensi', 'id_kompetensi');
    }
    public function pengerjaans()
    {
        return $this->hasMany(IdpKompetensiPengerjaan::class, 'id_idpKom');
    }
    public function metodeBelajars()
    {
        return $this->belongsToMany(MetodeBelajar::class, 'idp_kompetensi_metode_belajars', 'id_idpKom', 'id_metodeBelajar');
    }
}
