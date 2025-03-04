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

  /**
   * obtener comparativa de precios de presupuestos
   * @param int $period_id id del periodo a consultar
   * @return array array de comparativa de precios
  */
  public function comparePricesBetweenQuotations(int $period_id): array
  {
    $period = RequestForQuotationPeriod::with(['quotations.supplier', 'quotations.provisions'])
      ->where('id', $period_id)
      ->where('period_status_id', $this->getStatusClosed())
      ->first();

    $compare_provision_prices = [];
    $compare_pack_prices = [];

    // por cada presupuesto, preparar suministros y packs con proveedores y precios
    foreach ($period->quotations as $quotation) {

      // suministros
      foreach ($quotation->provisions as $provision) {

        /* asegurar suministros no duplicados */
        if (!isset($compare_provision_prices[$provision->id])) {

          /* suministro */
          $compare_provision_prices[$provision->id] = [
              'id_suministro'         => $provision->id,
              'nombre_suministro'     => $provision->provision_name,
              'marca'                 => $provision->trademark->provision_trademark_name,
              'tipo'                  => $provision->type->provision_type_name,
              'volumen'               => convert_measure($provision->provision_quantity, $provision->measure),
              'volumen_tag'           => $provision->measure->unit_symbol,
              'cantidad'              => $provision->pivot->quantity,
              'precios_por_proveedor' => []
          ];

        }

        /* por suministro: proveedor, precio, stock, presupuesto */
        $compare_provision_prices[$provision->id]['precios_por_proveedor'][] = [
          'id_proveedor'    => $quotation->supplier->id,
          'id_presupuesto'  => $quotation->id,
          'proveedor'       => $quotation->supplier->company_name,
          'cuit'            => $quotation->supplier->company_cuit,
          'tiene_stock'     => $provision->pivot->has_stock,
          'cantidad'        => $provision->pivot->quantity,
          'precio_unitario' => $provision->pivot->unit_price,
          'precio_total'    => $provision->pivot->total_price,
        ];

      }

      // packs
      foreach ($quotation->packs as $pack) {

        /* asegurar packs no duplicados */
        if (!isset($compare_pack_prices[$pack->id])) {

          /* pack */
          $compare_pack_prices[$pack->id] = [
              'id_pack'               => $pack->id,
              'nombre_pack'           => $pack->pack_name,
              'marca'                 => $pack->provision->trademark->provision_trademark_name,
              'tipo'                  => $pack->provision->type->provision_type_name,
              'volumen'               => convert_measure($pack->pack_quantity, $pack->provision->measure),
              'volumen_tag'           => $pack->provision->measure->unit_symbol,
              'cantidad'              => $pack->pivot->quantity,
              'precios_por_proveedor' => []
          ];

        }

        /* por pack: proveedor, precio, stock, presupuesto */
        $compare_pack_prices[$pack->id]['precios_por_proveedor'][] = [
          'id_proveedor'    => $quotation->supplier->id,
          'id_presupuesto'  => $quotation->id,
          'proveedor'       => $quotation->supplier->company_name,
          'cuit'            => $quotation->supplier->company_cuit,
          'tiene_stock'     => $pack->pivot->has_stock,
          'cantidad'        => $pack->pivot->quantity,
          'precio_unitario' => $pack->pivot->unit_price,
          'precio_total'    => $pack->pivot->total_price,
        ];

      }
    }

    // calcular estadísticas por suministro
    foreach ($compare_provision_prices as &$provision_price) {

      // filtrar solo precios del cada suministro del que se tenga precio unitario, precio total y stock
      $valid_prices = array_filter($provision_price['precios_por_proveedor'], function($item) {
        return $item['tiene_stock'] === 1 && $item['precio_unitario'] > 0 && $item['precio_total'] > 0;
      });

      // capturo la columna de precios unitarios
      $unit_prices = array_column($valid_prices, 'precio_unitario');

      // estadisticas de precio unitario
      if (count($unit_prices) > 0) {
        $provision_price['estadisticas_precio_unitario'] = [
          'precio_minimo'        => min($unit_prices),
          'precio_maximo'        => max($unit_prices),
          'precio_promedio'      => array_sum($unit_prices) / count($unit_prices),
          'cantidad_proveedores' => count($unit_prices)
        ];
      } else {
        $provision_price['estadisticas_precio_unitario'] = [
          'precio_minimo'        => 0,
          'precio_maximo'        => 0,
          'precio_promedio'      => 0,
          'cantidad_proveedores' => 0
        ];
      }

      // capturo la columna de precios totales
      $total_prices = array_column($valid_prices, 'precio_total');

      // estadisticas de precio total
      if (count($total_prices) > 0) {
        $provision_price['estadisticas_precio_total'] = [
          'precio_minimo'        => min($total_prices),
          'precio_maximo'        => max($total_prices),
          'precio_promedio'      => array_sum($total_prices) / count($total_prices),
          'cantidad_proveedores' => count($total_prices)
        ];
      } else {
        $provision_price['estadisticas_precio_total'] = [
          'precio_minimo'        => 0,
          'precio_maximo'        => 0,
          'precio_promedio'      => 0,
          'cantidad_proveedores' => 0
        ];
      }
    }

    // Calcular estadísticas por pack
    foreach ($compare_pack_prices as &$pack_price) {

      // filtrar solo precios del cada pack del que se tenga precio unitario, precio total y stock
      $valid_prices = array_filter($pack_price['precios_por_proveedor'], function($item) {
        return $item['tiene_stock'] === 1 && $item['precio_unitario'] > 0 && $item['precio_total'];
      });

      // capturo la columna de precios unitarios
      $unit_prices = array_column($valid_prices, 'precio_unitario');

      // estadisticas de precios unitarios
      if (count($unit_prices) > 0) {
        $pack_price['estadisticas_precio_unitario'] = [
          'precio_minimo'        => min($unit_prices),
          'precio_maximo'        => max($unit_prices),
          'precio_promedio'      => array_sum($unit_prices) / count($unit_prices),
          'cantidad_proveedores' => count($unit_prices)
        ];
      } else {
        $pack_price['estadisticas_precio_unitario'] = [
          'precio_minimo'        => 0,
          'precio_maximo'        => 0,
          'precio_promedio'      => 0,
          'cantidad_proveedores' => 0
        ];
      }

      // capturo la columna de precios totales
      $total_prices = array_column($valid_prices, 'precio_total');

      // estadisticas de precios totales
      if (count($total_prices) > 0) {
        $pack_price['estadisticas_precio_total'] = [
          'precio_minimo'        => min($total_prices),
          'precio_maximo'        => max($total_prices),
          'precio_promedio'      => array_sum($total_prices) / count($total_prices),
          'cantidad_proveedores' => count($total_prices)
        ];
      } else {
        $pack_price['estadisticas_precio_total'] = [
          'precio_minimo'        => 0,
          'precio_maximo'        => 0,
          'precio_promedio'      => 0,
          'cantidad_proveedores' => 0
        ];
      }
    }

    return ['provisions' => $compare_provision_prices, 'packs' => $compare_pack_prices];
  }

}
