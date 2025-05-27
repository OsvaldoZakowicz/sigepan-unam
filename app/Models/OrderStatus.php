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
   * obtener ID estado pendiente
   * @return int $id de estado pendiente
   */
  public static function ORDER_STATUS_PENDIENTE()
  {
    return OrderStatus::where('status', self::$ORDER_STATUS_PENDIENTE)->first()->id;
  }

  /**
   * obtener label textual del estado
   * @return string $status nombre del estado pendiente
   */
  public static function ORDER_STATUS_PENDIENTE_TEXT()
  {
    return self::$ORDER_STATUS_PENDIENTE;
  }

  /**
   * obtener ID estado entregado
   * @return int $id de estado entregado
   */
  public static function ORDER_STATUS_ENTREGADO()
  {
    return OrderStatus::where('status', self::$ORDER_STATUS_ENTREGADO)->first()->id;
  }

  /**
   * obtener label textual del estado
   * @return string $status nombre del estado entregado
   */
  public static function ORDER_STATUS_ENTREGADO_TEXT()
  {
    return self::$ORDER_STATUS_ENTREGADO;
  }

  /**
   * obtener ID estado cancelado
   * @return int $id de estado cancelado
   */
  public static function ORDER_STATUS_CANCELADO()
  {
    return OrderStatus::where('status', self::$ORDER_STATUS_CANCELADO)->first()->id;
  }

  /**
   * obtener label textual del estado
   * @return string $status nombre del estado cancelado
   */
  public static function ORDER_STATUS_CANCELADO_TEXT()
  {
    return self::$ORDER_STATUS_CANCELADO;
  }

  // * un estado de orden puede estar en muchas ordenes
  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'order_status_id', 'id');
  }
}
