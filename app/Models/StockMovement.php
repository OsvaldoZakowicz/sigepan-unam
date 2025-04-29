<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * modelo movimientos de stock
 */
class StockMovement extends Model
{
  use HasFactory;

  //* tipos de movimientos
  // todo: esto es temporal
  protected $movement_types = [
    'venta',
    'elaboracion',
    'perdida'
  ];

  protected $fillable = [
    'stock_id', // stock donde se aplico el movimiento
    'quantity', // cantidad del movimiento
    'movement_type', // tipo de movimiento
    'registered_at'  // fecha en la que se llevo a cabo el movimiento
  ];

  //* un movimiento es de un stock especifico
  public function stock(): BelongsTo
  {
    return $this->belongsTo(Stock::class, 'stock_id', 'id');
  }
}
