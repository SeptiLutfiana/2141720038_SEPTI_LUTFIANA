<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Panduan extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'panduans';

    // Primary key custom
    protected $primaryKey = 'id_panduan';

    // Field yang boleh diisi massal
    protected $fillable = [
        'judul',
        'isi',
    ];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'panduan_roles', 'id_panduan', 'id_role');
    }

    /**
     * Relasi one-to-many dengan PanduanRole
     */
    public function panduanRoles()
    {
        return $this->hasMany(PanduanRole::class, 'id_panduan', 'id_panduan');
    }
}
