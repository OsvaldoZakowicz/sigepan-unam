<?php

namespace App\Services\Supplier;

use App\Models\PeriodStatus;
use App\Models\RequestForQuotationPeriod;

class QuotationPeriodService
{

  // prefijo para el codigo de periodo
  protected $PREFIX = 'periodo_#';

  /**
   * obtener estado programado
   * @return int id del estado
  */
  public function getStatusScheduled(): int
  {
    $status_scheduled = PeriodStatus::where('status_name', 'programado')
      ->where('status_code', 0)->first();

    return $status_scheduled->id;
  }

  /**
   * obtener estado abierto
   * @return int id del estado
  */
  public function getStatusOpen(): int
  {
    $status_open = PeriodStatus::where('status_name', 'abierto')
      ->where('status_code', 1)->first();

    return $status_open->id;
  }

  /**
   * obtener estado cerrado
   * @return int id del estado
  */
  public function getStatusClosed(): int
  {
    $status_close = PeriodStatus::where('status_name', 'cerrado')
      ->where('status_code', 2)->first();

    return $status_close->id;
  }

  /**
   * obtener prefijo del codigo de periodo
   * @return string prefix
  */
  public function getPeriodCodePrefix(): string
  {
    return $this->PREFIX;
  }

  /**
   * obtener periodos de peticion de presupuestos que
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
   * obtener periodos de peticion de presupuestos que
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
