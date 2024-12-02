<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// todo: auditar

class Quotation extends Model
{
  use HasFactory;

  protected $fillable = [
    'quotation_code',
    'is_completed',
    'period_id',
    'supplier_id',
  ];

  //* un presupuesto pertenece a un periodo de peticion de presupuesto.
  public function period(): BelongsTo
  {
    return $this->belongsTo(RequestForQuotationPeriod::class, 'period_id', 'id');
  }

  //* un presupuesto pertenece a un proveedor.
  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
  }

  //* un presupuesto tiene muchos suministros.
  public function provisions(): BelongsToMany
  {
    return $this->belongsToMany(Provision::class)
      ->withPivot(['has_stock', 'price'])
      ->withTimestamps();
  }
}
