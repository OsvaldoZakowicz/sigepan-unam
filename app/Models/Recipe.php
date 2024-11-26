<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Recipe extends Model implements Auditable
{
  use HasFactory;
  use \OwenIt\Auditing\Auditable;

  protected $fillable = [
    'recipe_title',
    'recipe_yields',
    'recipe_portions',
    'recipe_preparation_time',
    'recipe_instructions',
    'recipe_short_description',
  ];

}
