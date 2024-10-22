<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Address extends Model implements Auditable
{
  use HasFactory;
  // paquete de auditoria
  use \OwenIt\Auditing\Auditable;

  protected $fillable = [
    'street',
    'number',
    'postal_code',
    'city',
  ];

  //* una direccion tiene un perfil asociado
  // address 1 : 1 profiles
  public function profile(): HasOne
  {
    return $this->hasOne(Profile::class);
  }

}
