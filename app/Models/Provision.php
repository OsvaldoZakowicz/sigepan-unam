<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// todo: auditar

class Provision extends Model
{
  use HasFactory;

  protected $fillable = [
    'provision_name',
    'provision_quantity',
    'provision_short_description',
    'provision_trademark_id',
    'provision_type_id',
    'measure_id',
  ];

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
      ->withTimestamps();
  }

  //* un suministro esta en muchos presupuestos (quotations).
  public function quotations(): BelongsToMany
  {
    return $this->belongsToMany(Quotation::class)
      ->withPivot(['has_stock', 'price'])
      ->withTimestamps();
  }
}
