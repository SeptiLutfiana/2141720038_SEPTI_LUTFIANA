<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Divisi extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'divisis';

    // Primary key custom
    protected $primaryKey = 'id_divisi';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_divisi',
        'keterangan',
    ];
    public function User()
  {
    return $this->hasOne(User::class, 'id_divisi');
  }
}
