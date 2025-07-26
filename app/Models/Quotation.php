<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Quotation extends Model implements Auditable
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

  protected $fillable = [
    'quotation_code',
    'is_completed',
    'period_id',
    'supplier_id',
  ];

  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
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
    return $this->belongsToMany(Provision::class, 'provision_quotation')
      ->using(ProvisionQuotation::class)
      ->withPivot(['has_stock', 'quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }

  //* un presupuesto tiene muchos packs
  public function packs(): BelongsToMany
  {
    return $this->belongsToMany(Pack::class, 'pack_quotation')
      ->using(PackQuotation::class)
      ->withPivot(['has_stock', 'quantity', 'unit_price', 'total_price'])
      ->withTimestamps();
  }
}
