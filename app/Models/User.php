<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;

  /**
   * 
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  // Nama tabelnya
  protected $table = 'users';

  // Primary key custom
  protected $primaryKey = 'id';

  protected $fillable = [
    'id_role',
    'id_jenjang',
    'id_jabatan',
    'id_angkatanpsp',
    'id_divisi',
    'id_penempatan',
    'id_LG',
    'id_semester',
    'npk',
    'name',
    'no_hp',
    'email',
    'password',
    'foto_profile',
    'status'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }
  public function roles()
  {
    return $this->belongsToMany(Role::class, 'user_roles', 'id_user', 'id_role');
  }
  public function jenjang()
  {
    return $this->belongsTo(Jenjang::class, 'id_jenjang');
  }
  public function jabatan()
  {
    return $this->belongsTo(Jabatan::class, 'id_jabatan');
  }
  public function angkatanPsp()
  {
    return $this->belongsTo(AngkatanPSP::class, 'id_angkatanpsp');
  }
  public function divisi()
  {
    return $this->belongsTo(Divisi::class, 'id_divisi');
  }
  public function penempatan()
  {
    return $this->belongsTo(Penempatan::class, 'id_penempatan');
  }
  public function learningGroup()
  {
    return $this->belongsTo(LearingGroup::class, 'id_LG');
  }
  public function userRoles()
  {
    return $this->hasMany(UserRole::class, 'id_user');
  }
  public function semester()
  {
    return $this->belongsTo(Semester::class, 'id_semester');
  }
  public function hasRole($roleName)
  {
    return $this->userRoles()->whereHas('role', function ($query) use ($roleName) {
      $query->where('nama_role', $roleName);
    })->exists();
  }
  public function idp()
  {
    return $this->hasMany(IDP::class, 'id_user', 'id');
  }
}
