<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
  use HasFactory;

  /**
   * * estados de una orden
   * respecto a la entrega del producto
   */
  protected static $ORDER_STATUS_PENDIENTE = 'pendiente';
  protected static $ORDER_STATUS_ENTREGADO = 'entregado';
  protected static $ORDER_STATUS_CANCELADO = 'cancelado';

  /**
   * atributos del estado
   */
  protected $fillable = [ 'status' ];

  /**
   * los atributos que deben convertirse
   */
  protected $casts = [ 'status' => 'string' ];

  /**
   * obtener estado pendiente
   * @return string
   */
  public static function ORDER_STATUS_PENDIENTE(): string
  {
    return self::$ORDER_STATUS_PENDIENTE;
  }

  /**
   * obtener estado entregado
   * @return string
   */
  public static function ORDER_STATUS_ENTREGADO(): string
  {
    return self::$ORDER_STATUS_ENTREGADO;
  }

  /**
   * obtener estado cancelado
   * @return string
   */
  public static function ORDER_STATUS_CANCELADO(): string
  {
    return self::$ORDER_STATUS_CANCELADO;
  }

  // * un estado de orden puede estar en muchas ordenes
  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'order_status_id', 'id');
  }
}
