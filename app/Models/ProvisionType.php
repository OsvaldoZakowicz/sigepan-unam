<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProvisionType extends Model
{
  use HasFactory;

  protected $fillable = [
    'provision_type_name',
    'provision_type_short_description',
  ];

  // * un tipo de suministro tiene muchos suministros
  public function provisions(): HasMany
  {
    return $this->hasMany(Provision::class, 'provision_type_id', 'id');
  }

  // * un tipo de suministro se asocia a muchas categorias de suministro
  public function provision_categories(): HasMany
  {
    return $this->hasMany(ProvisionCategory::class, 'provision_type_id', 'id');
  }
}
