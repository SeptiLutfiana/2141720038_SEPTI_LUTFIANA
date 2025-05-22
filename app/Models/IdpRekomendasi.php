<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdpRekomendasi extends Model
{
     protected $table = 'idp_rekomendasis';

    // Primary key custom
    protected $primaryKey = 'id_rekomendasi';
     protected $fillable = [
        'id_idp',
        'hasil_rekomendasi',
        'deskripsi_rekomendasi',
    ];
     public function idp()
     {
        return $this->belongsTo(IDP::class, 'id_idp');
    }
}
