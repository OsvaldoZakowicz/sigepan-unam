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

  /**
   * obtener comparativa de precios de presupuestos
   * @param int $period_id id del periodo a consultar
   * @return array $comparePrices array de comparativa de precios
  */
  public function comparePricesBetweenQuotations(int $period_id): array
  {
    $period = RequestForQuotationPeriod::with(['quotations.supplier', 'quotations.provisions'])
      ->where('id', $period_id)
      ->where('period_status_id', $this->getStatusClosed())
      ->first();

    $comparePrices = [];

    foreach ($period->quotations as $quotation) {

      foreach ($quotation->provisions as $provision) {

        if (!isset($comparePrices[$provision->id])) {

          $comparePrices[$provision->id] = [
              'id_suministro'         => $provision->id,
              'nombre_suministro'     => $provision->provision_name,
              'marca'                 => $provision->trademark->provision_trademark_name,
              'tipo'                  => $provision->type->provision_type_name,
              'volumen'               => $provision->provision_quantity,
              'volumen_tag'           => $provision->measure->measure_abrv,
              'cantidad'              => 'unidad/pack',
              'precios_por_proveedor' => []
          ];

        }

        $comparePrices[$provision->id]['precios_por_proveedor'][] = [
          'id_proveedor'  => $quotation->supplier->id,
          'proveedor'     => $quotation->supplier->company_name,
          'cuit'          => $quotation->supplier->company_cuit,
          'precio'        => $provision->pivot->price,
          'tiene_stock'   => $provision->pivot->has_stock
        ];

      }
    }

    // Calcular estadísticas por suministro
    foreach ($comparePrices as &$comparePrice) {
      $prices = array_column($comparePrice['precios_por_proveedor'], 'precio');
      $comparePrice['estadisticas'] = [
        'precio_minimo' => min($prices),
        'precio_maximo' => max($prices),
        'precio_promedio' => array_sum($prices) / count($prices),
        'cantidad_proveedores' => count($prices)
      ];
    }

    return $comparePrices;
  }

  /**
   * contar cuantos presupuestos fueron respondidos
   * en un periodo presupuestario.
   * @param int $period_id id del periodo sobre el que consultar
   * @return int conteo
  */
  public function countQuotationsFromPeriod(int $period_id): int
  {
    return Quotation::where('period_id', $period_id)
      ->where('is_completed', true)
      ->count();
  }
}
