<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Jabatan extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'jabatans';

    // Primary key custom
    protected $primaryKey = 'id_jabatan';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_jabatan',
        'keterangan',
    ];
    public function User()
  {
    return $this->hasOne(User::class, 'id_jabatan');
  }
}
