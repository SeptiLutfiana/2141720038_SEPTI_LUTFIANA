<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penempatan extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'penempatans';

    // Primary key custom
    protected $primaryKey = 'id_penempatan';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_penempatan',
        'keterangan',
    ];
    public function User()
  {
    return $this->hasOne(User::class, 'id_penempatan');
  }
}
