<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

  //* una receta pertenece a un producto
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id', 'id');
  }

}
