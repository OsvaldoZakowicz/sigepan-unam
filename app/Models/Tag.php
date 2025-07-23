<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Tag extends Model implements Auditable
{
  use HasFactory;
  use \OwenIt\Auditing\Auditable;

  /**
   * Eventos que deben ser auditados
   */
  protected $auditEvents = [
    'created',
    'updated', 
    'deleted',
  ];

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
