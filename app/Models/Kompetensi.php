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
        'id_jenjang',         // tambahan
        'id_jabatan',   
        'nama_kompetensi',
        'jenis_kompetensi',
        'keterangan',
    ];
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang', 'id_jenjang');
    }

    // Relasi ke Jabatan (satu kompetensi hanya punya satu jabatan)
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }
    public function idpKompetensis()
    {
        return $this->hasMany(IdpKompetensi::class, 'id_kompetensi');
    }
}
