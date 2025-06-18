<?php

namespace App\Services\Supplier;

use App\Models\PeriodStatus;
use App\Models\PreOrder;
use App\Models\PreOrderPeriod;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\Provision;
use App\Models\Pack;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class PreOrderPeriodService
{
  // prefijo para el codigo de periodo
  protected $PERIOD_PREFIX = 'periodo#';

  // prefijo para el codigo de presupuesto
  protected $PREORDER_PREFIX = 'preorden#';

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
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);

    // Generar el resto del código (longitud total sera 8 + longitud del prefijo)
    $randomPart = '';
    for ($i = 0; $i < 8; $i++) {
      $randomPart .= $characters[random_int(0, $charactersLength - 1)];
    }

    // Combinar prefijo con parte aleatoria
    $code = $this->PREORDER_PREFIX . $randomPart;

    // Verificar unicidad
    while ($this->codeExists($code)) {
      $randomPart = '';
      for ($i = 0; $i < 8; $i++) {
        $randomPart .= $characters[random_int(0, $charactersLength - 1)];
      }
      $code = $this->PREORDER_PREFIX . $randomPart;
    }

    return $code;
  }

  /**
   * metodo auxiliar para verificar la unicidad del codigo
   * * verifica que no exista un codigo similar en la base de datos
   * @param string $code codigo a verificar
   * @return bool existencia o no del codigo
   */
  private function codeExists(string $code): bool
  {
    return PreOrder::where('pre_order_code', $code)->exists();
  }

  /**
   * Genera pre ordenes basadas en los mejores precios de la comparativa o ranking de presupuestos
   * @param int $pre_order_period_id ID del período de pre ordenes
   * @param array $comparison_data Datos de la comparativa de precios o ranking de presupuestos
   * @return array Array de pre ordenes generadas
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

  /**
   * Genera pre ordenes basadas en los datos de pre orden del periodo,
   * cuando el periodo de pre orden no depende de un ranking de presupuestos previo
   * @param int $pre_order_period_id ID del período de pre ordenes
   * @return array Array de pre ordenes generadas
   */
  public function generatePreOrdersFromScratch(int $pre_order_period_id)
  {
    /**
     * [
     *  'provisions' => [
     *    [
     *      'provision' => App\Models\Provision,
     *      'supplier'  => App\Models\Supplier,
     *      'quantity'  => 'number...'
     *    ], ...
     *  ],
     *  'packs' => [
     *    [
     *      'pack'      => App\Models\Pack,
     *      'supplier'  => App\Models\Supplier,
     *      'quantity'  => 'number...'
     *    ], ...
     *  ]
     * ]
     */
    $provisions_and_packs = $this->getProvisionAndPacksData($pre_order_period_id);

    // agrupar suministros y packs por proveedor
    $suppliers_items = [];

    // Procesar suministros
    foreach ($provisions_and_packs['provisions'] as $provision_data) {
      $supplier = $provision_data['supplier'];
      $supplier_key = 'supplier' . $supplier->id;

      // Inicializar el array para el proveedor si no existe
      if (!isset($suppliers_items[$supplier_key])) {
        $suppliers_items[$supplier_key] = [
          'supplier' => $supplier,
          'provisions' => [],
          'packs' => []
        ];
      }

      // Agregar el suministro al array del proveedor
      $suppliers_items[$supplier_key]['provisions'][] = [
        'provision' => $provision_data['provision'],
        'quantity' => $provision_data['quantity']
      ];
    }

    // Procesar packs
    foreach ($provisions_and_packs['packs'] as $pack_data) {
      $supplier = $pack_data['supplier'];
      $supplier_key = 'supplier' . $supplier->id;

      // Inicializar el array para el proveedor si no existe
      if (!isset($suppliers_items[$supplier_key])) {
        $suppliers_items[$supplier_key] = [
          'supplier' => $supplier,
          'provisions' => [],
          'packs' => []
        ];
      }

      // Agregar el pack al array del proveedor
      $suppliers_items[$supplier_key]['packs'][] = [
        'pack' => $pack_data['pack'],
        'quantity' => $pack_data['quantity']
      ];
    }

    /**
     * formato de supplier_items:
      [
        'supplier1' => [
          'supplier' => App\Models\Supplier,
          'provisions' => [
            ['provision' => App\Models\Provision, 'quantity' => number],
            // ...más suministros
          ],
          'packs' => [
            ['pack' => App\Models\Pack, 'quantity' => number],
            // ...más packs
          ]
        ],
        'supplier2' => [
          // ...misma estructura
        ]
      ]
     */

    // pre ordenes generadas
    $generated_pre_orders = [];

    // crear pre ordenes por proveedor y crear items de pre orden
    foreach ($suppliers_items as $supplier_item) {

      // pre orden
      $pre_order = $this->createPreOrder($pre_order_period_id, $supplier_item['supplier']->id);

      // suministros de la pre orden
      foreach ($supplier_item['provisions'] as $item_provision) {

        // Obtener el precio del suministro para este proveedor
        $provision_price = $supplier_item['supplier']->provisions()
          ->where('provisions.id', $item_provision['provision']->id)
          ->first()
          ->pivot
          ->price ?? 0;

        $item_data = [
          'type'        =>  'provision',
          'id'          =>  $item_provision['provision']->id,
          'quantity'    =>  $item_provision['quantity'],
          'unit_price'  =>  $provision_price,
          'total_price' =>  $provision_price * $item_provision['quantity']

        ];

        // renglon pre orden para suministro
        $this->createPreOrderItem($pre_order, $item_data);
      }

      // packs de la pre orden
      foreach ($supplier_item['packs'] as $item_pack) {

        // Obtener el precio del pack para este proveedor
        $pack_price = $supplier_item['supplier']->packs()
          ->where('packs.id', $item_pack['pack']->id)
          ->first()
          ->pivot
          ->price ?? 0;

        $item_data = [
          'type'        =>  'pack',
          'id'          =>  $item_pack['pack']->id,
          'quantity'    =>  $item_pack['quantity'],
          'unit_price'  =>  $pack_price,
          'total_price' =>  $pack_price * $item_pack['quantity']
        ];

        // renglon pre orden para suministro
        $this->createPreOrderItem($pre_order, $item_data);
      }

      // Agregar la pre-orden con sus relaciones cargadas al array
      $generated_pre_orders[] = $pre_order->load(['packs', 'provisions']);
    }

    return $generated_pre_orders;
  }

  /**
   * Obtiene datos completos de suministros y packs de interes,
   * a partir de una lista de datos JSON de suministros y packs
   * @param int $pre_order_period_id ID del período de pre-ordenes
   * @return array array de datos: '['provisions' => [], 'packs' => []]'
   */
  public function getProvisionAndPacksData(int $pre_order_period_id): array
  {
    $preorder_period = PreOrderPeriod::findOrfail($pre_order_period_id);

    $array_data = json_decode($preorder_period->period_preorders_data, true);

    $provisions_and_packs = [
      'provisions'  =>  [],
      'packs'       =>  []
    ];

    $provisions_and_packs['provisions'] = Arr::map(
      array_filter($array_data, fn($item) => $item['provision_id'] !== null),
      function ($item) {
        // procesar suministros
        $provision = Provision::where('id', $item['provision_id'])->first();
        $supplier = Supplier::where('id', $item['supplier_id'])->first();

        return [
          'provision' => $provision,
          'supplier'  => $supplier,
          'quantity'  => $item['quantity']
        ];
      }
    );

    $provisions_and_packs['packs'] = Arr::map(
      array_filter($array_data, fn($item) => $item['pack_id'] !== null),
      function ($item) {
        // procesar pack
        $pack = Pack::where('id', $item['pack_id'])->first();
        $supplier = Supplier::where('id', $item['supplier_id'])->first();

        return [
          'pack'     => $pack,
          'supplier' => $supplier,
          'quantity' => $item['quantity'],
        ];
      }
    );

    return $provisions_and_packs;
  }

  /**
   * agrupa los suministrso y packs con mejores precios por proveedor
   * @param array $comparision_data datos de la comparativa de precios o ranking de presupuestos
   * @return array $best_prices
   */
  private function groupBestPricesBySupplier(array $comparison_data): array
  {
    $best_prices = [];

    // Procesar suministros
    foreach ($comparison_data['provisions'] as $provision) {

      $best_price = $this->findBestPrice($provision['precios_por_proveedor']);

      if ($best_price) {
        $supplier_id = $best_price['id_proveedor'];
        $best_prices[$supplier_id][] = [
          'type'          => 'provision',
          'id'            => $provision['id_suministro'],
          'quantity'      => $best_price['cantidad'],
          'unit_price'    => $best_price['precio_unitario'],
          'total_price'   => $best_price['precio_total'],
          'quotation_id'  => $best_price['id_presupuesto']
        ];
      }
    }

    // Procesar packs
    foreach ($comparison_data['packs'] as $pack) {

      $best_price = $this->findBestPrice($pack['precios_por_proveedor']);

      if ($best_price) {
        $supplier_id = $best_price['id_proveedor'];
        $best_prices[$supplier_id][] = [
          'type'          => 'pack',
          'id'            => $pack['id_pack'],
          'quantity'      => $best_price['cantidad'],
          'unit_price'    => $best_price['precio_unitario'],
          'total_price'   => $best_price['precio_total'],
          'quotation_id'  => $best_price['id_presupuesto']
        ];
      }
    }

    return $best_prices;
  }

  /**
   * encuentra el mejor precio
   */
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
   * crea una pre orden
   * @param int $pre_order_period_id periodo de pre ordenes
   * @param int $supplier_id proveedor objetivo
   * @param int|null $quotation_id presupuesto previo relacionado (opcional)
   * @return PreOrder
   */
  private function createPreOrder(int $pre_order_period_id, int $supplier_id, ?int $quotation_id = null): PreOrder
  {
    return PreOrder::create([
      'pre_order_period_id'     => $pre_order_period_id,
      'supplier_id'             => $supplier_id,
      'pre_order_code'          => $this->generateUniquePreorderCode(),
      'quotation_reference'     => $quotation_id ? Quotation::find($quotation_id)->quotation_code : null,
      'status'                  => PreOrder::getPendingStatus(),
      'is_completed'            => false,
      'is_approved_by_supplier' => false,
      'is_approved_by_buyer'    => false,
      'details'                 => null
    ]);
  }

  /**
   * para cada pre orden, crear los renglones suministro y/o pack a pedir
   * @param PreOrder $pre_order pre orden creada
   * @param array $item_data datos del renglon
   */
  private function createPreOrderItem(PreOrder $pre_order, array $item_data)
  {
    if ($item_data['type'] === 'provision') {

      // completa la tabla pre_order_provision
      $pre_order->provisions()->attach($item_data['id'], [
        'has_stock'             => true,
        'quantity'              => $item_data['quantity'],
        'alternative_quantity'  => 0,
        'unit_price'            => $item_data['unit_price'],
        'total_price'           => $item_data['total_price']
      ]);
    }

    if ($item_data['type'] === 'pack') {

      // completa la tabla pre_order_pack
      $pre_order->packs()->attach($item_data['id'], [
        'has_stock'             => true,
        'quantity'              => $item_data['quantity'],
        'alternative_quantity'  => 0,
        'unit_price'            => $item_data['unit_price'],
        'total_price'           => $item_data['total_price']
      ]);
    }
  }

  /**
   * Genera una vista previa de las pre-ordenes basadas en los mejores precios de un ranking previo
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
          'id'             => $provision->id,
          'provision_name' => $provision->provision_name,
          'trademark'      => $provision->trademark->provision_trademark_name,
          'type'           => $provision->type->provision_type_name,
          'quantity'       => $comparison_item['cantidad'], // Usar cantidad de la comparativa
          'measure'        => $provision->measure->unit_symbol,
          'volumen'        => $comparison_item['volumen'], // Agregar volumen
          'volumen_tag'    => $comparison_item['volumen_tag'], // Agregar volumen_tag
          'unit_price'     => $provision_item['unit_price'],
          'total_price'    => $provision_item['total_price']
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
          'id'          => $pack->id,
          'pack_name'   => $pack->pack_name,
          'trademark'   => $pack->provision->trademark->provision_trademark_name,
          'type'        => $pack->provision->type->provision_type_name,
          'quantity'    => $comparison_item['cantidad'], // Usar cantidad de la comparativa
          'measure'     => $pack->provision->measure->unit_symbol,
          'volumen'     => $comparison_item['volumen'], // Agregar volumen
          'volumen_tag' => $comparison_item['volumen_tag'], // Agregar volumen_tag
          'unit_price'  => $pack_item['unit_price'],
          'total_price' => $pack_item['total_price']
        ];
      }

      // Calcular totales
      $total_provisions = array_sum(array_column($provisions_details, 'total_price'));
      $total_packs = array_sum(array_column($packs_details, 'total_price'));
      $total_order = $total_provisions + $total_packs;

      // Crear la estructura de la pre-orden
      $preview_pre_orders[] = [
        'pre_order_data'            => [
          'supplier_id'             => $supplier_id,
          'pre_order_code'          => $this->generateTemporaryPreorderCode($supplier_id),
          'quotation_reference'     => $quotation->quotation_code,
          'status'                  => 'pendiente',
          'is_completed'            => false,
          'is_approved_by_supplier' => false,
          'is_approved_by_buyer'    => false,
          'total_amount'            => $total_order,
          'current_date'            => date('Y-m-d')
        ],
        'supplier' => [
          'id'            => $supplier->id,
          'company_name'  => $supplier->company_name,
          'company_cuit'  => $supplier->company_cuit,
          'contact_email' => $supplier->user->email,
          'contact_phone' => $supplier->phone_number
        ],
        'provisions'  => $provisions_details,
        'packs'       => $packs_details,
        'summary' => [
          'total_provisions'  => $total_provisions,
          'total_packs'       => $total_packs,
          'total_order'       => $total_order,
          'provisions_count'  => count($provisions_details),
          'packs_count'       => count($packs_details),
          'items_count'       => count($provisions_details) + count($packs_details)
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
   * NOTA: una pre orden respondida 'is_completed' = true puede tener un estado 'aprobado' o 'rechazado'
   * donde el estado 'aprobado' puede ser con cantidad alternativa en algun suministro o pack
   * y donde el estado 'rechazado' considera como no cubierto todo lo pedido
   * @param PreOrderPeriod $preorder_period periodo de pre ordenes
   */
  public function getUncoveredItems(PreOrderPeriod $preorder_period)
  {
    // obtener todas las pre ordenes para el periodo
    $completed_preorders = $preorder_period->pre_orders()->get();

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

      // Determinar cantidad no cubierta según el estado de la pre-orden
      if ($preorder->status === PreOrder::getRejectedStatus()) {
        // Si la pre-orden está rechazada, toda la cantidad solicitada es faltante
        $uncovered_quantity = $pack->pivot->quantity;
      } else {
        // Si la pre-orden está aprobada, calcular cantidad no cubierta solo si no hay stock
        if (!$pack->pivot->has_stock) {
          $uncovered_quantity = $pack->pivot->quantity - ($pack->pivot->alternative_quantity ?? 0);
        } else {
          continue; // Si hay stock, no hay cantidad no cubierta
        }
      }

      // Si hay cantidad no cubierta, agregar a la colección
      if ($uncovered_quantity > 0) {
        $uncovered_packs->push([
          'id_pack'               => $pack->pivot->pack_id,
          'nombre_pack'           => $pack->pack_name,
          'marca_pack'            => $pack->provision->trademark->provision_trademark_name,
          'tipo_pack'             => $pack->provision->type->provision_type_name,
          'cantidad_pack'         => $pack->pack_quantity,
          'unidad_pack'           => $pack->provision->measure,
          'cantidad_faltante'     => $uncovered_quantity,
          'id_preorden'           => $preorder->id,
          'proveedor_contactado'  => $preorder->supplier,
        ]);
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

      // Determinar cantidad no cubierta según el estado de la pre-orden
      if ($preorder->status === PreOrder::getRejectedStatus()) {
        // Si la pre-orden está rechazada, toda la cantidad solicitada es faltante
        $uncovered_quantity = $provision->pivot->quantity;
      } else {
        // Si la pre-orden está aprobada, calcular cantidad no cubierta solo si no hay stock
        if (!$provision->pivot->has_stock) {
          $uncovered_quantity = $provision->pivot->quantity - ($provision->pivot->alternative_quantity ?? 0);
        } else {
          continue; // Si hay stock, no hay cantidad no cubierta
        }
      }

      // Si hay cantidad no cubierta, agregar a la colección
      if ($uncovered_quantity > 0) {
        $uncovered_provisions->push([
          'id_suministro'         => $provision->pivot->provision_id,
          'nombre_suministro'     => $provision->provision_name,
          'marca_suministro'      => $provision->trademark->provision_trademark_name,
          'tipo_suministro'       => $provision->type->provision_type_name,
          'cantidad_suministro'   => $provision->provision_quantity,
          'unidad_suministro'     => $provision->measure,
          'cantidad_faltante'     => $uncovered_quantity,
          'id_preorden'           => $preorder->id,
          'proveedor_contactado'  => $preorder->supplier,
        ]);
      }
    }
  }

  /**
   * obtener proveedores alternativos para los suministros y
   * packs no cubiertos procesados en la funcion getUncoveredItems
   * @param array $uncovered_items suministros y packs no cubiertos
   * @param array|null $quotations_ranking ranking de presupuestos (puede no existir ranking previo)
   * @return array suministros y packs no cubiertos con proveedores alternativos
   */
  public function getAlternativeSuppliersForUncoveredItems(array $uncovered_items, ?array $quotations_ranking)
  {
    $uncovered_provisions_with_alternative_suppliers = $this->processUncoveredItems(
      $uncovered_items['uncovered_provisions'],
      $quotations_ranking ? $quotations_ranking['provisions'] : null,
      'id_suministro'
    );

    $uncovered_packs_with_alternative_suppliers = $this->processUncoveredItems(
      $uncovered_items['uncovered_packs'],
      $quotations_ranking ? $quotations_ranking['packs'] : null,
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
   * @param array|null $quotations_ranking ranking de presupuestos para suministros o packs (puede no existir ranking previo)
   * @param string $item_id_key clave para indicar si buscare alternativos para suministro o pack
   * @return Collection suministros o packs no cubiertos con proveedores alternativos
   */
  private function processUncoveredItems($uncovered_items, $quotations_ranking, $item_id_key)
  {
    return $uncovered_items->map(function ($item) use ($quotations_ranking, $item_id_key) {
      // Si hay ranking de presupuestos, buscar proveedores alternativos allí
      if ($quotations_ranking) {
        return $this->getAlternativeSuppliersFromRanking($item, $quotations_ranking, $item_id_key);
      }

      // Si no hay ranking, buscar proveedores alternativos en la BD
      return $this->getAlternativeSuppliersFromDB($item, $item_id_key);
    });
  }

  /**
   * obtener proveedores alternativos desde el ranking de presupuestos
   */
  private function getAlternativeSuppliersFromRanking($item, array $quotations_ranking, string $item_id_key): array
  {
    // Buscar el item en el ranking de presupuestos
    // extrae suministros o packs segun el id key indicado
    $item_budgets = collect($quotations_ranking)
      ->filter(function ($budget) use ($item, $item_id_key) {
        $item_id = $item[$item_id_key];
        return $budget[$item_id_key] === $item_id;
      })
      ->first();

    if (!$item_budgets) {
      $item['alternative_suppliers'] = [];
      return $item;
    }

    // Obtener proveedores del ranking, excluyendo el proveedor original
    $alternative_suppliers = collect($item_budgets['precios_por_proveedor'])
      ->filter(function ($provider_price) use ($item) {
        return $provider_price['id_proveedor'] !== $item['proveedor_contactado']->id;
      })
      ->sortBy('precio_unitario')
      ->values()
      ->toArray();

    $item['alternative_suppliers'] = $alternative_suppliers ?: [];

    return $item;
  }

  /**
   * obtener proveedores alternativos desde la base de datos
   */
  private function getAlternativeSuppliersFromDB($item, string $item_id_key): array
  {
    $model = $item_id_key === 'id_suministro' ? Provision::class : Pack::class;
    $itemId = $item[$item_id_key];
    $currentSupplierId = $item['proveedor_contactado']->id;

    // Obtener el item (provision o pack)
    $modelItem = $model::find($itemId);

    // Obtener proveedores alternativos que tienen este item
    $alternative_suppliers = $modelItem->suppliers()
      ->where('suppliers.id', '!=', $currentSupplierId)
      ->get()
      ->map(function ($supplier) use ($modelItem) {
        // Obtener el precio desde la tabla pivot
        $price = $supplier->pivot->price;

        return [
          'id_proveedor' => $supplier->id,
          'proveedor' => $supplier->company_name,
          'precio_unitario' => $price,
          'tiene_stock' => true, // Por defecto asumimos que tiene stock
          'id_presupuesto' => null // No hay presupuesto asociado
        ];
      })
      ->sortBy('precio_unitario')
      ->values()
      ->toArray();

    $item['alternative_suppliers'] = $alternative_suppliers;
    return $item;
  }
}
