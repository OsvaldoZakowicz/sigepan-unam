<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class RequestForQuotationPeriod extends Model implements Auditable
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
    'period_code',
    'period_start_at',
    'period_end_at',
    'period_short_description',
    'period_status_id'
  ];

  protected $casts = [
    'period_start_at' => 'date:d-m-Y',
    'period_end_at' => 'date:d-m-Y',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
  ];


  //* un periodo de peticion de presupuestos tiene un estado
  public function status(): BelongsTo
  {
    return $this->belongsTo(PeriodStatus::class, 'period_status_id', 'id');
  }

  //* un periodo de peticion de presupuestos se realiza para muchos suministros
  // fk es: period_id
  public function provisions(): BelongsToMany
  {
    return $this->belongsToMany(Provision::class, 'period_provision', 'period_id', 'provision_id')
      ->using(PeriodProvision::class)
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* un periodo de peticion de presupuestos se realiza para muchos packs
  // fk es: period_id
  public function packs(): BelongsToMany
  {
    return $this->belongsToMany(Pack::class, 'pack_period', 'period_id', 'pack_id')
      ->using(PackPeriod::class)
      ->withPivot('quantity')
      ->withTimestamps();
  }

  //* un periodo de peticion de presupuestos tiene muchos presupuestos (quotations)
  public function quotations(): HasMany
  {
    return $this->hasMany(Quotation::class, 'period_id', 'id');
  }

  //* un periodo de peticion de presupuestos puede tener un periodo de pre orden
  // request_for_quotation_period 1:0..1 pre_order_period
  public function preorder_period(): HasOne
  {
    return $this->hasOne(PreOrderPeriod::class, 'quotation_period_id', 'id');
  }
}
