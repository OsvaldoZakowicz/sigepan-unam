<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
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

  protected static $ORDER_PAYMENT_STATUS_APROBADO = 'aprobado';
  protected static $ORDER_PAYMENT_STATUS_RECHAZADO = 'rechazado';
  protected static $ORDER_PAYMENT_STATUS_PENDIENTE = 'pendiente';

  /**
   * atributos de una orden
   */
  protected $fillable = [
    'order_code',       // codigo de la orden
    'order_status_id',  // estado de la orden (respecto a los productos)
    'user_id',          // usuario que ordena
    'total_price',      // costo total de la orden
    'ordered_at',       // fecha de orden del pedido
    'delivered_at',     // fecha de entrega del pedido
    'payment_status'    // estado del pago
  ];

  /**
   * atributos que deben convertirse
   */
  protected $casts = [
    'order_code'   => 'string',
    'total_price'  => 'decimal:2',
    'ordered_at'   => 'datetime',
    'delivered_at' => 'datetime',
    'payment_status' => 'string',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  /**
   * Retorna el estado de pago "aprobado"
   */
  public static function ORDER_PAYMENT_STATUS_APROBADO(): string
  {
    return self::$ORDER_PAYMENT_STATUS_APROBADO;
  }

  /**
   * Retorna el estado de pago "rechazado"
   */
  public static function ORDER_PAYMENT_STATUS_RECHAZADO(): string
  {
    return self::$ORDER_PAYMENT_STATUS_RECHAZADO;
  }

  /**
   * Retorna el estado de pago "pendiente"
   */
  public static function ORDER_PAYMENT_STATUS_PENDIENTE(): string
  {
    return self::$ORDER_PAYMENT_STATUS_PENDIENTE;
  }

  // * una orden tiene un estado de orden
  public function status(): BelongsTo
  {
    return $this->belongsTo(OrderStatus::class, 'order_status_id', 'id');
  }

  // * una orden tiene productos
  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'order_product')
      ->using(OrderProduct::class)
      ->withPivot('order_quantity', 'unit_price', 'subtotal_price', 'details')
      ->withTimestamps();
  }

  // * una orden tiene una venta (o pago) asociado
  public function sale(): HasOne
  {
    return $this->hasOne(Sale::class, 'order_id', 'id');
  }

  // * una orden es de un usuario
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
