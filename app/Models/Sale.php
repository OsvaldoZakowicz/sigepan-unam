<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
  use HasFactory;

  /**
   * * sale_type
   * venta web: desde tienda online
   * venta presencial: desde el mostrador
   */
  protected static $SALE_TYPE_WEB = 'venta web';
  protected static $SALE_TYPE_PRESENCIAL = 'venta presencial';

  /**
   * * client_type
   * registrado: es cliente registrado en el sistema
   * no registrado.
   */
  protected static $CLIENT_TYPE_REGISTERED = 'cliente registrado';
  protected static $CLIENT_TYPE_UNREGISTERED = 'cliente no registrado';

  protected $fillable = [
    'order_id',           // id de la orden o pedido de compra (nullable)
    'user_id',            // id del usuario que compra (nullable)
    'client_type',        // tipo de cliente: registrado o no registrado
    'sale_type',          // tipo de venta: web o presencial
    'sold_on',            // fecha de la venta
    'payment_type',       // tipo de pago: efectivo, mercado pago, tarjeta, etc
    'payment_id',         // numero de transaccion MP
    'status',             // estado del pago MP
    'external_reference', // referencia externa al pago MP
    'merchant_order_id',  // MP
    'total_price',        // precio total de la venta
    'full_response',      // respuesta completa de MP (u otro medio de pago)
  ];

  /**
   * Los atributos que deben ser convertidos
   * @var array<string,string>
   */
  protected $casts = [
    'total_price' => 'decimal:2',
    'sold_on'     => 'datetime',
  ];

  // retorna el tipo de venta web
  public static function SALE_TYPE_WEB()
  {
    return self::$SALE_TYPE_WEB;
  }

  // retorna el tipo de venta presencial
  public static function SALE_TYPE_PRESENCIAL()
  {
    return self::$SALE_TYPE_PRESENCIAL;
  }

  // retorna tipo de cliente registrado
  public static function CLIENT_TYPE_REGISTERED()
  {
    return self::$CLIENT_TYPE_REGISTERED;
  }

  // retorna tipo de cliente no registrado
  public static function CLIENT_TYPE_UNREGISTERED()
  {
    return self::$CLIENT_TYPE_UNREGISTERED;
  }

  // * una venta puede estar asociada a un usuario
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  // * una venta puede pertenecer a una orden (pedido en la tienda)
  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'order_id', 'id');
  }

  // * una venta tiene muchos productos asociados
  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'product_sale')
      ->withPivot('sale_quantity', 'unit_price', 'subtotal_price', 'details')
      ->withTimestamps();
  }
}
