<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
  use HasFactory;

  /**
   * id de compra
   * id de suministro
   * id de pack
   * cantidad comprada (entero)
   * precio unitario
   * subtotal
   */
  protected $fillable = [
    'purchase_id',
    'provision_id',
    'pack_id',
    'item_count',
    'unit_price',
    'subtotal_price',
  ];

  /**
   * atributos que deben castearse
   */
  protected $casts = [
    'item_count'     => 'integer',
    'unit_price'     => 'decimal:2',
    'subtotal_price' => 'decimal:2'
  ];

  //* un detalle de compra pertenece a una compra
  public function purchase(): BelongsTo
  {
    return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
  }

  //* un detalle de compra se asocia a un suministro
  public function provision(): BelongsTo
  {
    return $this->belongsTo(Provision::class, 'provision_id', 'id');
  }

  //* un detalle de compra se asocia a un pack
  public function pack(): BelongsTo
  {
    return $this->belongsTo(Pack::class, 'pack_id', 'id');
  }
}
