<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kompetensi extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'kompetensis';

    // Primary key custom
    protected $primaryKey = 'id_kompetensi';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_kompetensi',
        'jenis_kompetensi',
        'keterangan',
    ];
    public function idpKompetensis()
    {
        return $this->hasMany(IdpKompetensi::class, 'id_kompetensi');
    }
}
