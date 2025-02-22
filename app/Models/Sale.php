<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_id',           // id de la orden o pedido de compra
    'payment_type',       // tipo de pago: efectivo, mercado pago, tarjeta, etc
    'payment_id',         // numero de transaccion MP
    'status',             // estado del pago MP
    'external_reference', // referencia externa al pago MP
    'merchant_order_id',  // MP
    'total_price',        // precio total de la venta
    'full_response',      // respuesta completa de MP (u otro medio de pago)
  ];

  // * una venta pertenece a una orden
  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'order_id', 'id');
  }
}
