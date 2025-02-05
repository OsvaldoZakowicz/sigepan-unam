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
    'measure_name',
    'measure_abrv',
    'measure_base',
    'measure_short_description',
  ];

  // * una unidad de medida esta presente en muchos suministros
  public function provisions(): HasMany
  {
    return $this->hasMany(Provision::class);
  }
}
