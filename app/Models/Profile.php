<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use App\Models\Gender;
use App\Models\Address;

class Profile extends Model
{
  use HasFactory;

  protected $fillable = [
    'first_name',
    'last_name',
    'dni',
    'birthdate',
    'phone_number',
    'gender_id',
    'user_id'
  ];

  //* un perfil pertenece a un usuario
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  //* un perfil tiene un genero
  public function gender(): BelongsTo
  {
    return $this->belongsTo(Gender::class);
  }

  //* un perfil tiene una relacion
  public function address(): HasOne
  {
    return $this->hasOne(Address::class);
  }
}
