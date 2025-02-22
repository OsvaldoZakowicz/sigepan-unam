<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
  use HasFactory;

  public const PENDIENTE = 'pendiente';
  public const EN_PROCESO = 'en proceso';
  public const FINALIZADO = 'finalizado';
  public const ENTREGADO = 'entregado';
  public const CANCELADO = 'cancelado';

  protected $fillable = [
    'id',
    'status',
  ];

  // * un estado de orden puede estar en muchas ordenes
  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'order_status_id', 'id');
  }
}
