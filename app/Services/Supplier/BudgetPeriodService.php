<?php

namespace App\Services\Supplier;

use App\Models\PeriodStatus;
use App\Models\RequestForQuotationPeriod;

class BudgetPeriodService
{
  //* obtener estado programado
  public function getStatusScheduled()
  {
    $status_scheduled = PeriodStatus::where('status_name', 'programado')
      ->where('status_code', 0)->first();

    return $status_scheduled->id;
  }

  //* obtener estado abierto
  public function getStatusOpen()
  {
    $status_open = PeriodStatus::where('status_name', 'abierto')
      ->where('status_code', 1)->first();

    return $status_open->id;
  }

  //* obtener estado cerrado
  public function getStatusClosed()
  {
    $status_close = PeriodStatus::where('status_name', 'abierto')
      ->where('status_code', 1)->first();

    return $status_close->id;
  }

  /**
   * * obtener periodos de peticion de presupuestos que
   * deben abrirse a la fecha actual.
  */
  public function getQuotationPeriodsToOpen()
  {
    // fecha actual
    $today = now()->format('Y-m-d');

    // periodos cuya fecha de inicio sea igual a la fecha actual, y estatus programado
    return RequestForQuotationPeriod::whereHas('status', function ($query) {
        $query->where('status_name', 'programado');
      })->where('period_start_at', $today)
        ->get();
  }

  /**
   * * obtener periodos de peticion de presupuestos que
   * deben cerrarse a la fecha actual.
  */
  public function getQuotationPeriodsToClose()
  {
    // fecha actual
    $today = now()->format('Y-m-d');

    // periodos cuya fecha de fin sea igual a la fecha actual, y estatus abierto
    return RequestForQuotationPeriod::whereHas('status', function ($query) {
        $query->where('status_name', 'abierto');
      })->where('period_end_at', $today)
        ->get();
  }
}
