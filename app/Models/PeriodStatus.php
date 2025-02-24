<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodStatus extends Model
{
  use HasFactory;

  protected $fillable = [
    'status_name',
    'status_code',
    'status_short_description',
  ];

  //* un estado de periodo de solicitud tiene muchos periodos de solicitud
  public function periods(): HasMany
  {
    return $this->hasMany(RequestForQuotationPeriod::class, 'period_status_id', 'id');
  }

   //* un estado de periodo de solicitud tiene muchos periodos de preorden
   public function preorder_periods(): HasMany
   {
     return $this->hasMany(PreOrderPeriod::class, 'period_status_id', 'id');
   }
}
