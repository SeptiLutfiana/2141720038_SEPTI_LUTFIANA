<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AngkatanPSP extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'angkatan_psps';

    // Primary key custom
    protected $primaryKey = 'id_angkatanpsp';

    // Field yang boleh diisi massal
    protected $fillable = [
        'bulan',
        'tahun',
    ];
    public function User()
{
    return $this->hasOne(User::class, 'id_angkatanpsp');
}

}
