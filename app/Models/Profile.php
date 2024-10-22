<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\User;
use App\Models\Gender;
use App\Models\Address;

class Profile extends Model implements Auditable
{
  use HasFactory;
  // paquete de auditoria
  use \OwenIt\Auditing\Auditable;

  protected $fillable = [
    'first_name',
    'last_name',
    'dni',
    'birthdate',
    'phone_number',
    'gender_id',
    'user_id',
    'address_id',
  ];

  //* un perfil pertenece a un usuario
  // el usuario puede o no tener perfil
  // profiles 0..1 : 1 users
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  //* muchos perfiles tienen un genero asociado
  // profiles 0,1..n : 1 genders
  public function gender(): BelongsTo
  {
    return $this->belongsTo(Gender::class);
  }

  //* a un perfil le pertenece una direccion
  // profiles 1 : 1 addresses
  public function address(): BelongsTo
  {
    return $this->belongsTo(Address::class);
  }

}
