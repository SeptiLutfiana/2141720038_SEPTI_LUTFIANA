<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'semesters';

    // Primary key custom
    protected $primaryKey = 'id_semester';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_semester',
        'keterangan',
    ];
     public function User()
  {
    return $this->hasOne(User::class, 'id_semester');
  }
}
