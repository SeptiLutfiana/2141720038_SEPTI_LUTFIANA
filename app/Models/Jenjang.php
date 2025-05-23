<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jenjang extends Model
{
  use HasFactory;

  // Nama tabelnya
  protected $table = 'jenjangs';

  // Primary key custom
  protected $primaryKey = 'id_jenjang';

  // Field yang boleh diisi massal
  protected $fillable = [
    'nama_jenjang',
    'keterangan',
  ];
  public function User()
  {
    return $this->hasOne(User::class, 'id_jenjang');
  }
  public function jabatans()
  {
    return $this->hasMany(Jabatan::class, 'id_jenjang');
  }
}
