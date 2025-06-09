<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PanduanRole extends Model
{
    use HasFactory;

    // Nama tabelnya
    protected $table = 'panduan_roles';

    // Primary key custom
    protected $primaryKey = 'id_panduan_role';

    // Field yang boleh diisi massal
    protected $fillable = [
        'id_panduan',
        'id_role',
    ];

    public function panduan()
    {
        return $this->belongsTo(Panduan::class, 'id_panduan', 'id_panduan');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }
}
