<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class TemplateApplay extends Model
{
    use HasFactory;

    protected $table = 'idp_template_applies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_idp_template',
        'id_user',
        'id_semester',
        'applied_at',
        'id_mentor',
        'id_jenjang',
        'id_jabatan',
        'id_angkatanpsp',
        'id_divisi',
        'id_penempatan',
        'id_LG',
    ];
    public function bankIdpTemplate()
    {
        return $this->belongsTo(IDP::class, 'id_idp_template', 'id_idp');
    }
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'id_user')->whereHas('roles', function ($query) {
            $query->where('roles.id_role', 4);
        });
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }
    public function mentor()
    {
        return $this->belongsTo(User::class, 'id_mentor')->whereHas('roles', function ($query) {
            $query->where('roles.id_role', 3);
        });
    }
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang', 'id_jenjang');
    }
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }
    public function angkatanpsp()
    {
        return $this->belongsTo(AngkatanPSP::class, 'id_angkatanpsp', 'id_angkatanpsp');
    }
    public function learningGroup()
    {
        return $this->belongsTo(LearingGroup::class, 'id_LG', 'id_LG');
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }
    public function penempatan()
    {
        return $this->belongsTo(Penempatan::class, 'id_penempatan', 'id_penempatan');
    }
}
