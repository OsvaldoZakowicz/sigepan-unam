<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Purchase extends Model implements Auditable
{
  use HasFactory;
  use \OwenIt\Auditing\Auditable;

  /**
   * Eventos que deben ser auditados
   */
  protected $auditEvents = [
    'created',
    'updated', 
    'deleted',
  ];

  /**
   * atributos de una compra
   */
  protected $fillable = [
    'supplier_id',              // id de proveedor
    'purchase_date',            // fecha de compra
    'total_price',              // precio total de la compra
    'purchase_reference_id',    // (nullable) id de preorden de referencia
    'purchase_reference_type',  // (nullable) modelo de preorden de referencia
  ];

  /**
   * atributos que deben castearse
   */
  protected $casts = [
    'purchase_date' => 'datetime',
    'total_price'   => 'decimal:2',
    'created_at'         => 'datetime',
    'updated_at'         => 'datetime',
  ];

  //* una compra pertenece a un proveedor
  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
  }

  //* una compra tiene muchos detalles de compra
  public function purchase_details(): HasMany
  {
    return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
  }

  //* una compra esta en muchos registros de existencia
  public function existences(): HasMany
  {
    return $this->hasMany(Existence::class, 'purchase_id', 'id');
  }
}
