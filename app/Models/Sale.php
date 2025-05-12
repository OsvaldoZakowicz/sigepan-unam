<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_id',           // id de la orden o pedido de compra (nullable)
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
    'total_price'      => 'decimal:2',
  ];

  // * una venta puede pertenecer a una orden (pedido en la tienda)
  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'order_id', 'id');
  }

  // * una venta tiene muchos productos asociados
  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'product_sale')
      ->withPivot('sale_quantity', 'unit_price', 'subtotal_price')
      ->withTimestamps();
  }
}
