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
  protected static $MOVEMENT_TYPE_ELABORACION      = 'elaboracion';     // positivo (crea)
  protected static $MOVEMENT_TYPE_VENTA            = 'venta';           // negativo
  protected static $MOVEMENT_TYPE_VENTA_CANCELADA  = 'venta cancelada'; // positivo (reintegra)
  protected static $MOVEMENT_TYPE_MERMA            = 'merma';           // negativo
  protected static $MOVEMENT_TYPE_PERDIDA          = 'perdida';         // negativo
  protected static $MOVEMENT_TYPE_VENCIMIENTO      = 'vencimiento';     // negativo
  protected static $MOVEMENT_TYPE_PEDIDO           = 'pedido';          // negativo
  protected static $MOVEMENT_TYPE_PEDIDO_CANCELADO = 'pedido cancelado';// positivo (reintegra)

  protected $fillable = [
    'stock_id',       // stock donde se aplico el movimiento
    'quantity',       // cantidad del movimiento (puede ser negativo para salidas)
    'movement_type',  // tipo de movimiento (string)
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
   * retorna el tipo de movimiento 'venta cancelada'
   * venta: - disminucion del stock por venta.
   * @return string
   */
  public static function MOVEMENT_TYPE_VENTA_CANCELADA(): string
  {
    return self::$MOVEMENT_TYPE_VENTA_CANCELADA;
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

  /**
   * retorna el tipo de movimiento 'vencimiento'
   * perdida: - disminucion del stock por vencimiento o falla en la produccion.
   * @return string
   */
  public static function MOVEMENT_TYPE_VENCIMIENTO(): string
  {
    return self::$MOVEMENT_TYPE_VENCIMIENTO;
  }

  /**
   * retorna el tipo de movimiento 'pedido'
   * perdida: - disminucion del stock por vencimiento o falla en la produccion.
   * @return string
   */
  public static function MOVEMENT_TYPE_PEDIDO(): string
  {
    return self::$MOVEMENT_TYPE_PEDIDO;
  }

  /**
   * retorna el tipo de movimiento 'pedido cancelado'
   * perdida: - disminucion del stock por vencimiento o falla en la produccion.
   * @return string
   */
  public static function MOVEMENT_TYPE_PEDIDO_CANCELADO(): string
  {
    return self::$MOVEMENT_TYPE_PEDIDO_CANCELADO;
  }

  /**
   * retorna un array de movimientos de tipo positivo
   * solo para fines de frontend.
   * @return array
   */
  public static function POSITIVE_MOVEMENTS(): array
  {
    return [
      self::$MOVEMENT_TYPE_ELABORACION,
      self::$MOVEMENT_TYPE_VENTA_CANCELADA,
      self::$MOVEMENT_TYPE_PEDIDO_CANCELADO,
    ];
  }

  /**
   * retorna un array de movimientos de tipo negativo
   * solo para fines de frontend.
   * @return array
   */
  public static function NEGATIVE_MOVEMENTS(): array
  {
    return [
      self::$MOVEMENT_TYPE_MERMA,
      self::$MOVEMENT_TYPE_PEDIDO,
      self::$MOVEMENT_TYPE_PERDIDA,
      self::$MOVEMENT_TYPE_VENTA,
      self::$MOVEMENT_TYPE_VENCIMIENTO,
    ];
  }


  //* un movimiento es de un stock especifico
  public function stock(): BelongsTo
  {
    return $this->belongsTo(Stock::class, 'stock_id', 'id');
  }
}
