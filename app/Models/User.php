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
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

  use SoftDeletes;

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
    'is_first_login'
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
      'deleted_at' => 'datetime',
    ];
  }

  /**
   * Scope para obtener solo usuarios activos (no eliminados)
   */
  public function scopeActive($query)
  {
    return $query->whereNull('deleted_at');
  }

  /**
   * Verificar si el usuario está eliminado (soft deleted)
   */
  public function isDeleted(): bool
  {
    return !is_null($this->deleted_at);
  }

  /**
   * Verificar si el usuario puede ser restaurado
   */
  public function canBeRestored(): bool
  {
    return $this->trashed();
  }

  //* un usuario tiene cero a un perfil asociado
  // users 1 : 0..1 profiles
  // fk user_id en profiles
  public function profile(): HasOne
  {
    return $this->hasOne(Profile::class);
  }

  //* un usuario tiene 0 a 1 un proveedor asociado
  // users 1 : 0..1 suppliers
  // fk user_id en suppliers
  public function supplier(): HasOne
  {
    return $this->hasOne(Supplier::class);
  }

  // * un usuario puede tener ventas asociadas
  // usuario con rol cliente
  public function sales(): HasMany
  {
    return $this->hasMany(Sale::class, 'user_id', 'id');
  }

  // * un usuario puede tener ordenes o pedidos asociados
  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'user_id', 'id');
  }
}
