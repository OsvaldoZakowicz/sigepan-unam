<?php

namespace App\Services\Supplier;

use App\Models\PeriodStatus;
use App\Models\Quotation;
use App\Models\RequestForQuotationPeriod;

class QuotationPeriodService
{

  // prefijo para el codigo de periodo
  protected $PERIOD_PREFIX = 'periodo_#';

  // prefijo para el codigo de presupuesto
  protected $QUOTATION_PREFIX = 'presupuesto_#';

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
    return $this->PERIOD_PREFIX;
  }

  /**
   * obtener periodos de peticion de presupuestos que
   * deben abrirse a la fecha actual, o una fecha anterior,
   * siempre que el estado sea programado.
  */
  public function getQuotationPeriodsToOpen()
  {
    // fecha actual
    $today = now()->format('Y-m-d');

    // periodos cuya fecha de inicio sea igual a la fecha actual, y estatus programado
    return RequestForQuotationPeriod::whereHas('status', function ($query) {
        $query->where('status_name', 'programado');
      })->where('period_start_at', '<=', $today)
        ->get();
  }

  /**
   * obtener periodos de peticion de presupuestos que
   * deben cerrarse a la fecha actual, o debieron cerrar
   * en una fecha anterior, siempre que el estado sea abierto.
  */
  public function getQuotationPeriodsToClose()
  {
    // fecha actual
    $today = now()->format('Y-m-d');

    // periodos cuya fecha de fin sea igual a la fecha actual, y estatus abierto
    return RequestForQuotationPeriod::whereHas('status', function ($query) {
        $query->where('status_name', 'abierto');
      })->where('period_end_at', '<=', $today)
        ->get();
  }

  /**
   * crear codigos de presupuesto unicos
   * @return string
  */
  public function generateUniqueQuotationCode(): string
  {
    // Conjunto de caracteres para generar el código aleatorio restante
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@$%&*=+';
    $charactersLength = strlen($characters);

    // Generar el resto del código (longitud total será 10 + longitud del prefijo)
    $randomPart = '';
    for ($i = 0; $i < 10; $i++) {
      $randomPart .= $characters[random_int(0, $charactersLength - 1)];
    }

    // Combinar prefijo con parte aleatoria
    $code = $this->QUOTATION_PREFIX . $randomPart;

    // Verificar unicidad
    while ($this->codeExists($code)) {
      $randomPart = '';
      for ($i = 0; $i < 10; $i++) {
        $randomPart .= $characters[random_int(0, $charactersLength - 1)];
      }
      $code = $this->QUOTATION_PREFIX . $randomPart;
    }

    return $code;
  }

  /**
   * metodo auxiliar para verificar la unicidad del codigo
   * @param string $code codigo a verificar
   * @return bool existencia o no del codigo
  */
  private function codeExists(string $code): bool
  {
    return Quotation::where('quotation_code', $code)->exists();
  }
}
