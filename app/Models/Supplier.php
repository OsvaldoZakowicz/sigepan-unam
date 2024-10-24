<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\User;
use App\Models\Address;

// todo: auditar
class Supplier extends Model implements Auditable
{
  use HasFactory;
  // paquete de auditoria
  use \OwenIt\Auditing\Auditable;

  protected $fillable = [
    'company_name',
    'company_cuit',
    'iva_condition',
    'phone_number',
    'short_description',
    'user_id',
    'address_id'
  ];

  //* a un proveedor le pertenece un usuario
  // suppliers 0..1 : 1 users
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  //* a un proveedor le pertenece una direccion
  // suppliers 1 : 1 addresses
  public function address(): BelongsTo
  {
    return $this->belongsTo(Address::class);
  }
}
