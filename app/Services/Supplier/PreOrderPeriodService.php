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
use Illuminate\Support\Collection;

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
   * obtener las pre ordenes no completadas (es decir, no respondidas)
   * de un periodo de pre ordenes
   * @param PreOrderPeriod $preorder_period periodo de pre ordenes
   */
  public function getPreOrdersToReject(PreOrderPeriod $preorder_period)
  {
    return $preorder_period->pre_orders()
      ->where('is_completed', false)
      ->get();
  }

  /**
   * retorna true si existe en el periodo dado al menos una pre orden completada por el proveedor
   * pero pendiente de evaluacion por parte del comprador (panaderia, gerente)
   * @param PreOrderPeriod $preorder_period periodo de pre ordenes
   */
  public function getPreOrdersPending(PreOrderPeriod $preorder_period)
  {
    return $preorder_period->pre_orders()
      ->where('is_completed', true)
      ->where('status', PreOrder::getPendingStatus())
      ->exists();
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
        $comparison_item = collect($comparison_data['provisions'])->first(function ($item) use ($provision_item) {
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
        $comparison_item = collect($comparison_data['packs'])->first(function ($item) use ($pack_item) {
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

  /**
   * obtener suministros y packs no cubiertos en las pre ordenes respondidas
   * @param PreOrderPeriod $preorder_period periodo de pre ordenes
   */
  public function getUncoveredItems(PreOrderPeriod $preorder_period)
  {
    // obtener todas las pre ordenes completadas para el periodo
    $completed_preorders = $preorder_period->pre_orders()
      ->where('is_completed', true)
      ->get();

    // inicializar colecciones para packs y suministros
    $uncovered_provisions = collect();
    $uncovered_packs = collect();

    // procesar cada pre orden
    foreach ($completed_preorders as $preorder) {

      // procesar packs no cubiertos
      $this->processUncoveredPacks($preorder, $uncovered_packs);

      // procesar suministros no cubiertos
      $this->processUncoveredProvisions($preorder, $uncovered_provisions);
    }

    return [
      'uncovered_provisions'  => $uncovered_provisions,
      'uncovered_packs'       => $uncovered_packs,
    ];
  }

  /**
   * procesar packs con cantidades no cubiertas en la pre orden
   * @param PreOrder $preorder pre orden
   * @param Collection $uncovered_packs referencia a coleccion de packs no cubiertos
   */
  private function processUncoveredPacks(PreOrder $preorder, Collection &$uncovered_packs)
  {
    $packs = $preorder->packs;

    foreach ($packs as $pack) {

      // packs marcados como 'sin stock'
      if (!$pack->pivot->has_stock) {
        // cantidad no cubierta
        $uncovered_quantity = $pack->pivot->quantity - ($pack->pivot->alternative_quantity ?? 0);

        // si la cantidad no cubierta es mayor a 0
        if ($uncovered_quantity > 0) {
          $uncovered_packs->push([
            'id_pack'               => $pack->pivot->pack_id,   // id del pack no cubierto
            'nombre_pack'           => $pack->pack_name,        // nombre del pack no cubierto
            'marca_pack'            => $pack->provision->trademark->provision_trademark_name, // marca del pack
            'tipo_pack'             => $pack->provision->type->provision_type_name,           // tipo del pack
            'cantidad_pack'         => $pack->pack_quantity,      // volumen del pack
            'unidad_pack'           => $pack->provision->measure, // unidad de medida del pack
            'cantidad_faltante'     => $uncovered_quantity,       // cantidad no cubierta
            'id_preorden'           => $preorder->id,             // preorden donde se pidio el pack
            'proveedor_contactado'  => $preorder->supplier->id,   // proveedor contactado
          ]);
        }
      }
    }
  }

  /**
   * procesar suministros con cantidades no cubiertas en la pre orden
   * @param PreOrder $preorder pre orden
   * @param Collection $uncovered_provisions referencia a coleccion de suministros no cubiertos
   */
  private function processUncoveredProvisions(PreOrder $preorder, Collection &$uncovered_provisions)
  {
    $provisions = $preorder->provisions;

    foreach ($provisions as $provision) {

      // suministros marcados como 'sin stock'
      if (!$provision->pivot->has_stock) {
        // cantidad no cubierta
        $uncovered_quantity = $provision->pivot->quantity - ($provision->pivot->alternative_quantity ?? 0);

        // si la cantidad no cubierta es mayor a 0
        if ($uncovered_quantity > 0) {
          $uncovered_provisions->push([
            'id_suministro'         => $provision->pivot->provision_id,  // id del suministro no cubierto
            'nombre_suministro'     => $provision->provision_name,       // nombre del pack no cubierto
            'marca_suministro'      => $provision->trademark->provision_trademark_name, // marca del suministro
            'tipo_suministro'       => $provision->type->provision_type_name,           // tipo del suministro
            'cantidad_suministro'   => $provision->provision_quantity,   // volumen del suministro
            'unidad_suministro'     => $provision->measure,              // unidad de medida del suministro
            'cantidad_faltante'     => $uncovered_quantity,              // cantidad no cubierta
            'id_preorden'           => $preorder->id,                    // preorden donde se pidio el suministro
            'proveedor_contactado'  => $preorder->supplier->id,          // proveedor contactado
          ]);
        }
      }
    }
  }

  /**
   * obtener proveedores alternativos para los suministros y
   * packs no cubiertos procesados en la funcion getUncoveredItems
   * @param array $uncovered_items suministros y packs no cubiertos
   * @param array $quotations_ranking ranking de presupuestos
   * @return array suministros y packs no cubiertos con proveedores alternativos
   */
  public function getAlternativeSuppliersForUncoveredItems(array $uncovered_items, array $quotations_ranking)
  {
    $uncovered_provisions_with_alternative_suppliers = $this->processUncoveredItems(
      $uncovered_items['uncovered_provisions'],
      $quotations_ranking['provisions'],
      'id_suministro'
    );

    $uncovered_packs_with_alternative_suppliers = $this->processUncoveredItems(
      $uncovered_items['uncovered_packs'],
      $quotations_ranking['packs'],
      'id_pack'
    );

    return [
      'uncovered_provisions_with_alternative_suppliers' => $uncovered_provisions_with_alternative_suppliers,
      'uncovered_packs_with_alternative_suppliers'      => $uncovered_packs_with_alternative_suppliers,
    ];
  }

  /**
   * procesar suministros y packs no cubiertos, contrastar con el ranking de presupuestos
   * y obtener proveedores alternativos
   * @param Collection $uncovered_items suministros o packs no cubiertos
   * @param array $quotations_ranking ranking de presupuestos para suministros o packs
   * @param string $item_id_key clave para obtener el id del item, 'id_suministro' o 'id_pack'
   * @return Collection suministros o packs no cubiertos con proveedores alternativos
   */
  private function processUncoveredItems($uncovered_items, $quotations_ranking, $item_id_key)
  {
    return $uncovered_items->map(function ($item) use ($quotations_ranking, $item_id_key) {
      // Buscar el item en el ranking de presupuestos
      $item_budgets = collect($quotations_ranking)
        ->filter(function ($budget) use ($item, $item_id_key) {
          // Usar la clave correcta del item según si es suministro o pack
          $item_id = $item[$item_id_key];
          return $budget[$item_id_key] === $item_id;
        })
        ->first();

      if (!$item_budgets) {
        $item['alternative_suppliers'] = null;
        return $item;
      }

      // Obtener proveedores del ranking, excluyendo el proveedor original
      $alternative_suppliers = collect($item_budgets['precios_por_proveedor'])
        ->filter(function ($provider_price) use ($item) {
          return $provider_price['id_proveedor'] !== $item['proveedor_contactado'];
        })
        // Ordenar por precio unitario de menor a mayor
        ->sortBy('precio_unitario')
        ->values()
        ->toArray();

      $item['alternative_suppliers'] = $alternative_suppliers ?: null;
      return $item;
    });
  }
}
