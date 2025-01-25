<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pack extends Model
{
  use HasFactory;

  protected $fillable = [
    'pack_name',
    'pack_units',
    'pack_quantity',
    'provision_id'
  ];

  // * un pack pertenece a un suministro
  // o un pack se forma con unidades de un mismo suministro
  public function provision(): BelongsTo
  {
    return $this->belongsTo(Provision::class);
  }

  // * un pack es provisto por muchos proveedores
  // packs n : n suppliers
  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany(Supplier::class)
      ->withPivot('price')->withTimestamps();
  }
}
