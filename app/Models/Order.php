<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_code',
    'order_status_id',
    'user_id',
    'employee_id',
    'order_origin',
    'total_price',
    'delivered_at',
  ];

  // * una orden tiene un estado de orden
  public function status(): BelongsTo
  {
    return $this->belongsTo(OrderStatus::class, 'order_status_id', 'id');
  }

  // * una orden tiene productos
  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'order_product')
      ->withPivot('quantity', 'unit_price', 'subtotal_price')
      ->withTimestamps();
  }

  // * una orden tiene una venta (o pago) asociado
  public function sale(): HasOne
  {
    return $this->hasOne(Sale::class, 'order_id', 'id');
  }
}
