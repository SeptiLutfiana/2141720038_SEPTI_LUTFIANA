<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IDP extends Model
{
    use HasFactory;
    // Nama tabelnya
    protected $table = 'idps';

    // Primary key custom
    protected $primaryKey = 'id_idp';

    protected $fillable = [
        'id_user',
        'id_mentor',
        'id_supervisor',
        'proyeksi_karir',
        'waktu_mulai',
        'waktu_selesai',
        'status_approval_mentor',
        'status_pengajuan_idp',
        'saran_idp',
        'status_pengerjaan',
        'is_template',
        'deskripsi_idp',
    ];
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'id_user')->whereHas('roles', function ($query) {
            $query->where('roles.id_role', 4);
        });
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'id_mentor')->whereHas('roles', function ($query) {
            $query->where('roles.id_role', 3);
        });
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'id_supervisor')->whereHas('roles', function ($query) {
            $query->where('roles.id_role', 2);
        });
    }
    public function idpKompetensis()
    {
        return $this->hasMany(IdpKompetensi::class, 'id_idp');
    }
    public function rekomendasis()
    {
        return $this->hasMany(IdpRekomendasi::class, 'id_idp');
    }
}
