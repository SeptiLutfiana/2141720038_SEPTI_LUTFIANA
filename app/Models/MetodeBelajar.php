<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodeBelajar extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'metode_belajars';

    // Primary key custom
    protected $primaryKey = 'id_metodeBelajar';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_metodeBelajar',
        'keterangan',
    ];
    public function idpKompetensis()
    {
        return $this->belongsToMany(IdpKompetensi::class, 'idp_kompetensi_metode_belajars', 'id_metodeBelajar', 'id_idpKom');
    }
}
