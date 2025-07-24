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
  use SoftDeletes;
  use \OwenIt\Auditing\Auditable;

   /**
   * Eventos que deben ser auditados
   */
  protected $auditEvents = [
    'created',
    'updated', 
    'deleted',
    'restored',
  ];

  protected $fillable = [
    'street',
    'number',
    'postal_code',
    'city',
  ];

  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
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
