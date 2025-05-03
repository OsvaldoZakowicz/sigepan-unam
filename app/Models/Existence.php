<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Existence extends Model
{
  use HasFactory;


  /**
   * * movement_type
   * compra - (suma existencias)
   * elaboracion - (resta existencias )
   * perdida - (resta existencias)
   */

  /**
   * id de suministro (packs son suministros agrupados)
   * id de compra
   * tipo movimiento
   * fecha de registro
   * cantidad en existencias AFECTADAS (decimal: Kg, g, L, mL, ...) (puede ser negativo)
   */
  protected $fillable = [
    'provision_id',
    'purchase_id',
    'movement_type',
    'registered_at',
    'quantity_amount',
  ];

  /**
   * atributos que deben castearse
   */
  protected $casts = [
    'registered_at'   => 'timestamp',
    'quantity_amount' => 'decimal:2',
  ];

  //* un registro de existencias puede ser de una compra
  public function purchase(): HasMany
  {
    return $this->hasMany(Purchase::class, 'purchase_id', 'id');
  }

  //* un registro de existencias es de un suministro
  public function provision(): BelongsTo
  {
    return $this->belongsTo(Provision::class, 'provision_id', 'id');
  }
}
