<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Provision extends Model implements Auditable
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
    'provision_name',
    'provision_quantity',
    'provision_short_description',
    'provision_trademark_id',
    'provision_category_id',
    'provision_type_id',
    'measure_id',
  ];

  /**
   * atributos que deben convertise
   */
  protected $casts = [
    'provision_name' => 'string',
    'provision_quantity' => 'decimal:2',
    'provision_short_description' => 'string',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

   /**
   * obtiene el estado del suministro como string para presentacion
   */
  public function getStatusAttribute(): string
  {
    return $this->deleted_at ? 'borrado' : 'activo';
  }

  // * un suministro pertenece a una marca
  public function trademark(): BelongsTo
  {
    return $this->belongsTo(ProvisionTrademark::class, 'provision_trademark_id', 'id');
  }

  // * un suministro pertenece a un tipo
  public function type(): BelongsTo
  {
    return $this->belongsTo(ProvisionType::class, 'provision_type_id', 'id');
  }

  // * un suministro pertenece a una unidad de medida
  public function measure(): BelongsTo
  {
    return $this->belongsTo(Measure::class);
  }

  // * un suministro es provisto por muchos provedores
  // provisions n : n suppliers
  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany(Supplier::class, 'provision_supplier')
      ->using(ProvisionSupplier::class)
      ->withPivot('id', 'price')
      ->withTimestamps();
  }

  //* un suministro participa en muchas solicitudes de presupuesto.
  public function periods(): BelongsToMany
  {
    return $this->belongsToMany(RequestForQuotationPeriod::class, 'period_provision', 'provision_id', 'period_id')
      ->using(PeriodProvision::class)
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* un suministro esta en muchos presupuestos (quotations).
  public function quotations(): BelongsToMany
  {
    return $this->belongsToMany(Quotation::class, 'provision_quotation')
      ->using(ProvisionQuotation::class)
      ->withPivot(['has_stock', 'quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }

  // * un suministro puede estar en muchos packs que le pertenecen
  // o tambien un suministro se agrupa en packs de diversas unidades.
  public function packs(): HasMany
  {
    return $this->hasMany(Pack::class);
  }

  // * un suministro tiene una categoria
  public function provision_category(): BelongsTo
  {
    return $this->belongsTo(ProvisionCategory::class, 'provision_category_id', 'id');
  }

  //* un suministro tiene muchas pre ordenes
  public function pre_orders(): BelongsToMany
  {
    return $this->belongsToMany(PreOrder::class, 'pre_order_provision', 'provision_id', 'pre_order_id')
      ->using(PreOrderProvision::class)
      ->withPivot(['has_stock', 'quantity', 'alternative_quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }

  //* un suministro esta asociado a muchos detalles de compra
  public function purchase_details(): HasMany
  {
    return $this->hasMany(PurchaseDetail::class, 'provision_id', 'id');
  }

  //* un suministro tiene muchos registros de existencias
  // NOTA incluye existencias de packs
  public function existences(): HasMany
  {
    return $this->hasMany(Existence::class, 'provision_id', 'id');
  }
}
