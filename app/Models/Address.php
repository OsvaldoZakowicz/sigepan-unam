<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Profile;

class Address extends Model
{
  use HasFactory;

  protected $fillable = [
    'street',
    'number',
    'postal_code',
    'city',
    'profile_id'
  ];

  //* una direccion pertenece a un perfil
  public function profile(): BelongsTo
  {
    return $this->belongsTo(Profile::class);
  }
}
