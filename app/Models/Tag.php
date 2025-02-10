<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
  use HasFactory;

  protected $fillable = [
    'tag_name',
  ];

  //* un tag tiene productos
  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'product_tag')
      ->withTimestamps();
  }
}
