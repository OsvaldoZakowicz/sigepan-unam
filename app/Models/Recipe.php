<?php

namespace App\Models;

use App\Casts\TimeWithoutSeconds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Recipe extends Model implements Auditable
{
  use HasFactory;
  use \OwenIt\Auditing\Auditable;
  use SoftDeletes;

  protected $fillable = [
    'recipe_title',
    'recipe_yields',
    'recipe_portions',
    'recipe_preparation_time',
    'recipe_instructions',
    'product_id',
  ];

  /**
   * los atributos que deben ser transformados
   * @var array<string,string>
   */
  protected $casts = [
    'recipe_yields'   => 'integer',
    'recipe_portions' => 'integer',
    'recipe_preparation_time' => TimeWithoutSeconds::class,
  ];

  //* una receta pertenece a un producto
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id', 'id');
  }

  //* una receta tiene muchas categorias asociadas
  public function provision_categories(): BelongsToMany
  {
    return $this->belongsToMany(ProvisionCategory::class, 'category_recipe', 'recipe_id', 'category_id')
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* una receta se usa para elaborar muchos stocks
  public function stocks(): HasMany
  {
    return $this->hasMany(Stock::class, 'recipe_id', 'id');
  }

}
