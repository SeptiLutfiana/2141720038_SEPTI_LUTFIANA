<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LearingGroup extends Model
{
  use HasFactory;

  // Nama tabelnya
  protected $table = 'learning_groups';

  // Primary key custom
  protected $primaryKey = 'id_LG';

  // Field yang boleh diisi massal
  protected $fillable = [
    'nama_LG',
    'keterangan',
  ];
  public function User()
  {
    return $this->hasOne(User::class, 'id_LG');
  }
  // app/Models/LearningGroup.php
  public function idps()
  {
    return $this->hasMany(IDP::class, 'id_LG', 'id_LG');
  }
}
