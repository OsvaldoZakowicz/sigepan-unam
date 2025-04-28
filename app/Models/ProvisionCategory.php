<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProvisionCategory extends Model
{
  use HasFactory;

  protected $fillable = [
    'provision_category_name',
    'provision_category_is_editable',
    'measure_id',
    'provision_type_id'
  ];

  // * una categoria tiene una unidad de medida
  public function measure(): BelongsTo
  {
    return $this->belongsTo(Measure::class, 'measure_id', 'id');
  }

  // * una categoria tiene un tipo de suministro
  public function provision_type(): BelongsTo
  {
    return $this->belongsTo(ProvisionType::class, 'provision_type_id', 'id');
  }

  // * una categoria tiene muchos suministros asociados
  public function provisions(): HasMany
  {
    return $this->hasMany(Provision::class, 'provision_category_id', 'id');
  }

  //* una categoria tiene muchas recetas asociadas
  // relacion n : n con recetas
  public function recipes(): BelongsToMany
  {
    return $this->belongsToMany(Recipe::class, 'category_recipe', 'category_id', 'recipe_id')
      ->withPivot('quantity')
      ->withTimestamps();
  }
}
