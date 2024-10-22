<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // paquete de roles y permisos
use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements MustVerifyEmail, Auditable
{
    use HasFactory;
    use Notifiable;

    /** paquete de roles y permisos, incluye la relacion entre modelos y tablas
     * la relacion es polimorfica entre:
     * users n:n model_has_roles
     * users n:n model_has_permissions
     */
    use HasRoles;

    /** paquete de auditoria
     */
    use \OwenIt\Auditing\Auditable;

    /**
     * * excluir de auditoria
     */
    protected $auditExclude = [
      'password',
      'remember_token'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    //* un usuario tiene cero a un perfil asociado
    // users 1 : 0..1 profiles
    // fk user_id en profiles
    public function profile(): HasOne
    {
      return $this->hasOne(Profile::class);
    }

}
