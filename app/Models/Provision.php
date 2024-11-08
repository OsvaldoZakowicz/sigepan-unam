<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    return $this->belongsTo(ProvisionTrademark::class);
  }

  // * un suministro pertenece a un tipo
  public function type(): BelongsTo
  {
    return $this->belongsTo(ProvisionType::class);
  }

  // * un suministro pertenece a una unidad de medida
  public function measure(): BelongsTo
  {
    return $this->belongsTo(Measure::class);
  }
}
