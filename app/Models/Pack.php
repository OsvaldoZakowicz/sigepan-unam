<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pack extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'pack_name',
    'pack_units',
    'pack_quantity',
    'provision_id'
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
    return $this->belongsToMany(Supplier::class)
      ->withPivot('price')->withTimestamps();
  }

  //* un pack participa en muchas solicitudes de presupuesto
  // packs n : n request_for_quotation_periods
  public function periods(): BelongsToMany
  {
    return $this->belongsToMany(RequestForQuotationPeriod::class, 'pack_period', 'pack_id', 'period_id')
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* un pack esta en muchos presupuestos (quotations).
  public function quotations(): BelongsToMany
  {
    return $this->belongsToMany(Quotation::class)
      ->withPivot(['has_stock', 'quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }
}
