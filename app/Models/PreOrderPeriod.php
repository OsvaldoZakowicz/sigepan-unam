<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreOrderPeriod extends Model
{
  use HasFactory;

  protected $fillable = [
    'quotation_period_id',
    'period_code',
    'period_start_at',
    'period_end_at',
    'period_short_description',
    'period_status_id',
    'period_preorders_data', // json, datos de pre ordenes
  ];

  //* un periodo de pre ordenes tiene un estado
  public function status(): BelongsTo
  {
    return $this->belongsTo(PeriodStatus::class, 'period_status_id', 'id');
  }

  //* un periodo de pre ordenes puede pertenecer a un periodo de presupuestos
  public function quotation_period(): BelongsTo
  {
    return $this->belongsTo(RequestForQuotationPeriod::class, 'quotation_period_id', 'id');
  }

  //* un periodo de pre ordenes tiene muchas pre ordenes
  public function pre_orders(): HasMany
  {
    return $this->hasMany(PreOrder::class, 'pre_order_period_id', 'id');
  }

}
