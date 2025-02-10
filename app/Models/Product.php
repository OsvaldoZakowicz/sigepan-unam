<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'product_name',
    'product_price',
    'product_short_description'
  ];

  //* un producto tiene muchos tags de producto
  public function tags(): BelongsToMany
  {
    return $this->belongsToMany(Tag::class, 'product_tag')
      ->withTimestamps();
  }
}
