<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RequestForQuotationPeriod extends Model
{
  use HasFactory;

  protected $fillable = [
    'period_code',
    'period_start_at',
    'period_end_at',
    'period_short_description',
    'period_status_id'
  ];

  /**
   * Get the attributes that should be cast.
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'period_start_at' => 'datetime:d-m-Y',
      'period_end_at' => 'datetime:d-m-Y',
    ];
  }

  //* un periodo de solicitud de presupuestos tiene un estado
  public function status(): BelongsTo
  {
    return $this->belongsTo(PeriodStatus::class, 'period_status_id', 'id');
  }

  //* un periodo de solicitud de presupuestos se realiza para muchos suministros
  // mi kf es: period_id
  public function provisions(): BelongsToMany
  {
    return $this->belongsToMany(Provision::class, 'period_provision', 'period_id', 'provision_id')
      ->withTimestamps();
  }
}
