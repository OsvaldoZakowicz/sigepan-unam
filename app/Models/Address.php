<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model implements Auditable
{
  use HasFactory;
  // paquete de auditoria
  use \OwenIt\Auditing\Auditable;

  use SoftDeletes;

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

  //* una direccion tiene un proveedor asociado
  // address 1 : 1 suppliers
  // fk address_id en suppliers
  public function supplier(): HasOne
  {
    return $this->hasOne(Supplier::class);
  }
}
