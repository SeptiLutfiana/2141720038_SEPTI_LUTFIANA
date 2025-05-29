<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class TemplateApplay extends Model
{
    use HasFactory;

    protected $table = 'idp_template_applies';

    protected $fillable = [
        'id_idp_template',
        'id_user',
        'id_semester', 
        'applied_at',
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
}
