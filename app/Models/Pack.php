<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Pack extends Model implements Auditable
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
    'restored'
  ];

  protected $fillable = [
    'pack_name',
    'pack_units',
    'pack_quantity',
    'provision_id'
  ];

  /**
   * atributos que deben convertise
   */
  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];


  // * un pack pertenece a un suministro
  // o un pack se forma con unidades de un mismo suministro
  public function provision(): BelongsTo
  {
    return $this->belongsTo(Provision::class);
  }

  // * un pack es provisto por muchos proveedores
  // packs n : n suppliers
  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany(Supplier::class, 'pack_supplier')
      ->using(PackSupplier::class)
      ->withPivot('id', 'price')
      ->withTimestamps();
  }

  //* un pack participa en muchas solicitudes de presupuesto
  // packs n : n request_for_quotation_periods
  public function periods(): BelongsToMany
  {
    return $this->belongsToMany(RequestForQuotationPeriod::class, 'pack_period', 'pack_id', 'period_id')
      ->using(PackPeriod::class)
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* un pack esta en muchos presupuestos (quotations).
  public function quotations(): BelongsToMany
  {
    return $this->belongsToMany(Quotation::class, 'pack_quotation')
      ->using(PackQuotation::class)
      ->withPivot(['has_stock', 'quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }

  //* un pack esta en muchas pre ordenes
  public function pre_orders(): BelongsToMany
  {
    return $this->belongsToMany(PreOrder::class, 'pre_order_pack', 'pack_id', 'pre_order_id')
      ->withPivot(['has_stock', 'quantity', 'alternative_quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }

  //* un pack esta asociado a muchos detalles de compras
  public function purchase_details(): HasMany
  {
    return $this->hasMany(PurchaseDetail::class, 'pack_id', 'id');
  }
}
