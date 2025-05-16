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

  /**
   * * tipos de movimientos
   * elaboracion: incremento del stock por elaborar el producto.
   * venta: disminucion del stock por venta.
   * merma: disminucion del stock por una elaboracion menor a la esperada.
   * perdida: disminucion del stock por vencimiento o falla en la produccion.
   */
  protected static $MOVEMENT_TYPE_ELABORACION = 'elaboracion';
  protected static $MOVEMENT_TYPE_VENTA = 'venta';
  protected static $MOVEMENT_TYPE_MERMA = 'merma';
  protected static $MOVEMENT_TYPE_PERDIDA = 'perdida';

  protected $fillable = [
    'stock_id',       // stock donde se aplico el movimiento
    'quantity',       // cantidad del movimiento (puede ser negativo para salidas)
    'movement_type',  // tipo de movimiento
    'registered_at',  // fecha en la que se llevo a cabo el movimiento
    'movement_reference_id',   // id de referencia (stock, venta, otro)
    'movement_reference_type', // modelo de referencia (stock, venta, otro)
  ];

  /**
   * los atributos que deben ser convertidos
   * @var array<string,string>
   */
  protected $casts = [
    'quantity'      => 'integer',
    'registered_at' => 'datetime',
  ];

  /**
   * retorna el tipo de movimiento 'elaboracion'
   * elaboracion: + incremento del stock por elaborar el producto.
   * @return string
   */
  public static function MOVEMENT_TYPE_ELABORACION(): string
  {
    return self::$MOVEMENT_TYPE_ELABORACION;
  }

  /**
   * retorna el tipo de movimiento 'venta'
   * venta: - disminucion del stock por venta.
   * @return string
   */
  public static function MOVEMENT_TYPE_VENTA(): string
  {
    return self::$MOVEMENT_TYPE_VENTA;
  }

  /**
   * retorna el tipo de movimiento 'merma'
   * merma: - disminucion del stock por una elaboracion menor a la esperada.
   * @return string
   */
  public static function MOVEMENT_TYPE_MERMA(): string
  {
    return self::$MOVEMENT_TYPE_MERMA;
  }

  /**
   * retorna el tipo de movimiento 'perdida'
   * perdida: - disminucion del stock por vencimiento o falla en la produccion.
   * @return string
   */
  public static function MOVEMENT_TYPE_PERDIDA(): string
  {
    return self::$MOVEMENT_TYPE_PERDIDA;
  }


  //* un movimiento es de un stock especifico
  public function stock(): BelongsTo
  {
    return $this->belongsTo(Stock::class, 'stock_id', 'id');
  }
}
