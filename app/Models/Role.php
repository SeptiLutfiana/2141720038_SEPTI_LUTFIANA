<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'roles';

    // Primary key custom
    protected $primaryKey = 'id_role';

    // Field yang boleh diisi massal
    protected $fillable = [
        'nama_role',
        'keterangan',
    ];

    /**
     * Relasi banyak ke banyak antara Role dan User
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'id_role', 'id_user');
    }

    /**
     * Relasi satu ke banyak antara Role dan UserRole
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'id_role');
    }
}
