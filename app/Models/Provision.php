<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provision extends Model
{
  use HasFactory;
  use SoftDeletes;

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
    'provision_name'              => 'string',
    'provision_quantity'          => 'decimal:2',
    'provision_short_description' => 'string'
  ];

  /**
   * obtener atributo deleted_at, y presentarlo
   * deleted_at es una fecha o null, cuando tiene una fecha indica el borrado
  */
  protected function deletedAt(): Attribute
  {
    return Attribute::make(
      get: fn (string|null $value) => $value ? 'borrado' : 'activo'
    );
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
    return $this->belongsToMany(Supplier::class)
      ->withPivot('id', 'price')->withTimestamps();
  }

  //* un suministro participa en muchas solicitudes de presupuesto.
  public function periods(): BelongsToMany
  {
    return $this->belongsToMany(RequestForQuotationPeriod::class, 'period_provision', 'provision_id', 'period_id')
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* un suministro esta en muchos presupuestos (quotations).
  public function quotations(): BelongsToMany
  {
    return $this->belongsToMany(Quotation::class)
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
