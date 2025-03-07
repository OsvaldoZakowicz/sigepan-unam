<?php

namespace App\Services\Supplier;

use App\Models\PeriodStatus;
use App\Models\PreOrder;
use App\Models\PreOrderPeriod;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\Provision;
use App\Models\Pack;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PreOrderPeriodService
{
  // prefijo para el codigo de periodo
  protected $PERIOD_PREFIX = 'periodo_#';

  // prefijo para el codigo de presupuesto
  protected $PREORDER_PREFIX = 'preorden_#';

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
   * obtener periodos de peticion de preordenes que
   * deben abrirse a la fecha actual, o una fecha anterior,
   * siempre que el estado sea programado.
   */
  public function getPreOrderPeriodsToOpen()
  {
    // fecha actual
    $today = now()->format('Y-m-d');

    // periodos cuya fecha de inicio sea igual a la fecha actual, y estatus programado
    return PreOrderPeriod::whereHas('status', function ($query) {
      $query->where('status_name', 'programado');
    })->where('period_start_at', '<=', $today)
      ->get();
  }

  /**
   * obtener periodos de peticion de preordenes que
   * deben cerrarse a la fecha actual, o debieron cerrar
   * en una fecha anterior, siempre que el estado sea abierto.
   */
  public function getPreOrdersPeriodsToClose()
  {
    // fecha actual
    $today = now()->format('Y-m-d');

    // periodos cuya fecha de fin sea igual a la fecha actual, y estatus abierto
    return PreOrderPeriod::whereHas('status', function ($query) {
      $query->where('status_name', 'abierto');
    })->where('period_end_at', '<=', $today)
      ->get();
  }

  /**
   * crear codigos de preorden unicos
   * @return string
   */
  public function generateUniquePreorderCode(): string
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
    $code = $this->PREORDER_PREFIX . $randomPart;

    // Verificar unicidad
    while ($this->codeExists($code)) {
      $randomPart = '';
      for ($i = 0; $i < 10; $i++) {
        $randomPart .= $characters[random_int(0, $charactersLength - 1)];
      }
      $code = $this->PREORDER_PREFIX . $randomPart;
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
    return PreOrder::where('pre_order_code', $code)->exists();
  }

  /**
   * Genera pre-órdenes basadas en los mejores precios de la comparativa
   * @param int $pre_order_period_id ID del período de pre-órdenes
   * @param array $comparison_data Datos de la comparativa de precios
   * @return array Array de pre-ordenes generadas
   */
  public function generatePreOrdersFromRanking(int $pre_order_period_id, array $comparison_data): array
  {
    // Agrupar los mejores precios por proveedor
    $best_prices_by_supplier = $this->groupBestPricesBySupplier($comparison_data);

    $generated_pre_orders = [];

    // Crear pre-ordenes para cada proveedor
    foreach ($best_prices_by_supplier as $supplier_id => $items) {
      $pre_order = $this->createPreOrder(
        $pre_order_period_id,
        $supplier_id,
        $items[0]['quotation_id'] // Usamos el ID del presupuesto asociado
      );

      // Crear items para la pre-orden
      foreach ($items as $item) {
        $this->createPreOrderItem($pre_order, $item);
      }

      $generated_pre_orders[] = $pre_order->load(['packs', 'provisions']);
    }

    return $generated_pre_orders;
  }

  private function groupBestPricesBySupplier(array $comparison_data): array
  {
    $best_prices = [];

    // Procesar suministros
    foreach ($comparison_data['provisions'] as $provision) {
      $best_price = $this->findBestPrice($provision['precios_por_proveedor']);

      if ($best_price) {
        $supplier_id = $best_price['id_proveedor'];
        $best_prices[$supplier_id][] = [
          'type' => 'provision',
          'id' => $provision['id_suministro'],
          'quantity' => $best_price['cantidad'],
          'unit_price' => $best_price['precio_unitario'],
          'total_price' => $best_price['precio_total'],
          'quotation_id' => $best_price['id_presupuesto']
        ];
      }
    }

    // Procesar packs
    foreach ($comparison_data['packs'] as $pack) {
      $best_price = $this->findBestPrice($pack['precios_por_proveedor']);

      if ($best_price) {
        $supplier_id = $best_price['id_proveedor'];
        $best_prices[$supplier_id][] = [
          'type' => 'pack',
          'id' => $pack['id_pack'],
          'quantity' => $best_price['cantidad'],
          'unit_price' => $best_price['precio_unitario'],
          'total_price' => $best_price['precio_total'],
          'quotation_id' => $best_price['id_presupuesto']
        ];
      }
    }

    return $best_prices;
  }

  private function findBestPrice(array $supplier_prices): ?array
  {
    $valid_prices = array_filter(
      $supplier_prices,
      fn($price) =>
      $price['tiene_stock'] === 1 &&
        $price['precio_unitario'] > 0 &&
        $price['precio_total'] > 0
    );

    if (empty($valid_prices)) {
      return null;
    }

    return array_reduce($valid_prices, function ($best, $current) {
      if (!$best || $current['precio_total'] < $best['precio_total']) {
        return $current;
      }
      return $best;
    });
  }

  /**
   * 'pre_order_period_id', // fk pre_order_periods
   * 'supplier_id', //fk suppliers
   * 'pre_order_code', //varchar unico
   * 'quotation_reference', //varchar nullable
   * 'status', //enum = ['pendiente', 'aprobado', 'rechazado']
   * 'is_approved_by_supplier', //boolean
   * 'is_approved_by_buyer', //boolean
   * 'details', //json, nullable
   */
  private function createPreOrder(
    int $pre_order_period_id,
    int $supplier_id,
    int $quotation_id
    ): PreOrder {
    return PreOrder::create([
      'pre_order_period_id' => $pre_order_period_id,
      'supplier_id' => $supplier_id,
      'pre_order_code' => $this->generateUniquePreorderCode(),
      'quotation_reference' => Quotation::find($quotation_id)->quotation_code,
      'status' => 'pendiente',
      'is_completed' => false,
      'is_approved_by_supplier' => false,
      'is_approved_by_buyer' => false,
      'details' => null
    ]);
  }

  private function createPreOrderItem(PreOrder $pre_order, array $item_data)
  {
    if ($item_data['type'] === 'provision') {

      // completa la tabla pre_order_provision
      $pre_order->provisions()->attach($item_data['id'], [
        'has_stock' => true,
        'quantity' => $item_data['quantity'],
        'alternative_quantity' => 0,
        'unit_price' => $item_data['unit_price'],
        'total_price' => $item_data['total_price']
      ]);
    }

    if ($item_data['type'] === 'pack') {

      // completa la tabla pre_order_pack
      $pre_order->packs()->attach($item_data['id'], [
        'has_stock' => true,
        'quantity' => $item_data['quantity'],
        'alternative_quantity' => 0,
        'unit_price' => $item_data['unit_price'],
        'total_price' => $item_data['total_price']
      ]);
    }
  }

  /**
   * Genera una vista previa de las pre-órdenes basadas en los mejores precios
   * @param array $comparison_data Datos de la comparativa de precios
   * @return array Array de vista previa de pre-órdenes
   */
  public function previewPreOrders(array $comparison_data): array
  {
    // Agrupar los mejores precios por proveedor
    $best_prices_by_supplier = $this->groupBestPricesBySupplier($comparison_data);

    $preview_pre_orders = [];

    // Crear estructura de vista previa para cada proveedor
    foreach ($best_prices_by_supplier as $supplier_id => $items) {
      // Obtener datos del proveedor
      $supplier = Supplier::find($supplier_id);
      $quotation_id = $items[0]['quotation_id'];
      $quotation = Quotation::find($quotation_id);

      // Separar los ítems en suministros y packs
      $provisions = array_filter($items, fn($item) => $item['type'] === 'provision');
      $packs = array_filter($items, fn($item) => $item['type'] === 'pack');

      // Obtener detalles completos de suministros
      // no necesito cantidad alternativa
      $provisions_details = [];
      foreach ($provisions as $provision_item) {
        $provision = Provision::find($provision_item['id']);
        // Encontrar el item en comparison_data
        $comparison_item = collect($comparison_data['provisions'])->first(function($item) use ($provision_item) {
          return $item['id_suministro'] == $provision_item['id'];
        });

        $provisions_details[] = [
          'id' => $provision->id,
          'provision_name' => $provision->provision_name,
          'trademark' => $provision->trademark->provision_trademark_name,
          'type' => $provision->type->provision_type_name,
          'quantity' => $comparison_item['cantidad'], // Usar cantidad de la comparativa
          'measure' => $provision->measure->unit_symbol,
          'volumen' => $comparison_item['volumen'], // Agregar volumen
          'volumen_tag' => $comparison_item['volumen_tag'], // Agregar volumen_tag
          'unit_price' => $provision_item['unit_price'],
          'total_price' => $provision_item['total_price']
        ];
      }

      // Obtener detalles completos de packs
      // no necesito cantidad alternativa
      $packs_details = [];
      foreach ($packs as $pack_item) {
        $pack = Pack::find($pack_item['id']);
        // Encontrar el item en comparison_data
        $comparison_item = collect($comparison_data['packs'])->first(function($item) use ($pack_item) {
          return $item['id_pack'] == $pack_item['id'];
        });

        $packs_details[] = [
          'id' => $pack->id,
          'pack_name' => $pack->pack_name,
          'trademark' => $pack->provision->trademark->provision_trademark_name,
          'type' => $pack->provision->type->provision_type_name,
          'quantity' => $comparison_item['cantidad'], // Usar cantidad de la comparativa
          'measure' => $pack->provision->measure->unit_symbol,
          'volumen' => $comparison_item['volumen'], // Agregar volumen
          'volumen_tag' => $comparison_item['volumen_tag'], // Agregar volumen_tag
          'unit_price' => $pack_item['unit_price'],
          'total_price' => $pack_item['total_price']
        ];
      }

      // Calcular totales
      $total_provisions = array_sum(array_column($provisions_details, 'total_price'));
      $total_packs = array_sum(array_column($packs_details, 'total_price'));
      $total_order = $total_provisions + $total_packs;

      // Crear la estructura de la pre-orden
      $preview_pre_orders[] = [
        'pre_order_data' => [
          'supplier_id' => $supplier_id,
          'pre_order_code' => $this->generateTemporaryPreorderCode($supplier_id),
          'quotation_reference' => $quotation->quotation_code,
          'status' => 'pendiente',
          'is_completed' => false,
          'is_approved_by_supplier' => false,
          'is_approved_by_buyer' => false,
          'total_amount' => $total_order,
          'current_date' => date('Y-m-d')
        ],
        'supplier' => [
          'id' => $supplier->id,
          'company_name' => $supplier->company_name,
          'company_cuit' => $supplier->company_cuit,
          'contact_email' => $supplier->user->email,
          'contact_phone' => $supplier->phone_number
        ],
        'provisions' => $provisions_details,
        'packs' => $packs_details,
        'summary' => [
          'total_provisions' => $total_provisions,
          'total_packs' => $total_packs,
          'total_order' => $total_order,
          'provisions_count' => count($provisions_details),
          'packs_count' => count($packs_details),
          'items_count' => count($provisions_details) + count($packs_details)
        ]
      ];
    }

    return $preview_pre_orders;
  }

  /**
   * Genera un código temporal de pre-orden para la vista previa
   * @param int $supplier_id ID del proveedor
   * @return string Código temporal
   */
  private function generateTemporaryPreorderCode(int $supplier_id): string
  {
    return 'PREVIEW-' . date('Ymd') . '-S' . $supplier_id . '-' . substr(uniqid(), -5);
  }
}
