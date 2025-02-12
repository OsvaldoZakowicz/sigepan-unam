<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Measure extends Model implements Auditable
{
  use HasFactory;
  use \OwenIt\Auditing\Auditable;

  protected $fillable = [
    'unit_name',
    'base_value',
    'unit_symbol',
    'conversion_unit',
    'conversion_factor',
    'conversion_symbol',
    'short_description',
  ];

  // * una unidad de medida esta presente en muchos suministros
  public function provisions(): HasMany
  {
    return $this->hasMany(Provision::class);
  }

  // * una unidad de medida esta asociada a muchas categorias de suministro
  public function provision_categories(): HasMany
  {
    return $this->hasMany(ProvisionCategory::class, 'measure_id', 'id');
  }
}
