<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PreOrder extends Model
{
  use HasFactory;

  protected $fillable = [
    'pre_order_period_id', // fk pre_order_periods
    'supplier_id', //fk suppliers
    'pre_order_code', //varchar unico
    'quotation_reference', //varchar nullable
    'status', //enum = ['pendiente', 'aprobado', 'rechazado']
    'is_completed', // boolean
    'is_approved_by_supplier', //boolean
    'is_approved_by_buyer', //boolean
  ];

  //* una pre orden pertenece a un periodo de pre ordenes
  public function pre_order_period(): BelongsTo
  {
    return $this->belongsTo(PreOrderPeriod::class, 'pre_order_period_id', 'id');
  }

  //* una pre orden pertenece a un proveedor
  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
  }

  //* una pre orden tiene muchos packs de suministros
  public function packs(): BelongsToMany
  {
    return $this->belongsToMany(Pack::class, 'pre_order_pack', 'pre_order_id', 'pack_id')
      ->withPivot(['has_stock','quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }

  //* una pre orden tiene muchos suministros
  public function provisions(): BelongsToMany
  {
    return $this->belongsToMany(Provision::class, 'pre_order_provision', 'pre_order_id', 'provision_id')
      ->withPivot(['has_stock','quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }
}
