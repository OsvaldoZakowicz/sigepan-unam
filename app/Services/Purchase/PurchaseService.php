<?php

namespace App\Services\Purchase;

use App\Models\PreOrder;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Existence;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
  /**
   * retornar lista de proveedores activos
   */
  public function getActiveSuppliers()
  {
    return Supplier::where('status_is_active', '1')
      ->orderBy('company_name')->get();
  }

  /**
   * procesar una pre orden y recuperar sus datos de orden definitiva
   * para obtener suministros que fueron comprados.
   * @param PreOrder $preorder
   * @return array
   */
  public function getOrderData(PreOrder $preorder): array
  {
    // Obtener el proveedor
    $supplier = $preorder->supplier;
    // Obtener datos del json order
    $order_data = json_decode($preorder->order, true);

    // Fecha de la orden
    $order_date = $order_data['order_date'];
    // codigo de orden
    $order_code = $order_data['order_code'];
    // total $ de la orden
    $order_total = $order_data['total'];

    // Procesar items (suministros y packs)
    $items = collect($order_data['items'])->map(function ($item) {

      if ($item['item_type'] === 'provision') {

        $provision    = Provision::find($item['item_id']);
        $trademark    = $provision->trademark->provision_trademark_name;
        $type         = $provision->type->provision_type_name;
        $unit_volume  = convert_measure_value($provision->provision_quantity, $provision->measure);
        $total_volume = convert_measure_value($provision->provision_quantity * (int)$item['item_quantity'], $provision->measure);

      } else if ($item['item_type'] === 'pack') {

        $pack = Pack::find($item['item_id']);
        $trademark    = $pack->provision->trademark->provision_trademark_name;
        $type         = $pack->provision->type->provision_type_name;
        $unit_volume  = convert_measure_value($pack->pack_quantity, $pack->provision->measure);
        $total_volume = convert_measure_value($pack->pack_quantity * (int)$item['item_quantity'], $pack->provision->measure);

      }

      // * NOTA: el helper convert_measure_value() retorna: array{symbol:string, value:float}
      // symbol: es el indicador del volumen (kg, L, mL, g, ...), y value es el volumen numerico.

      return [
        'item_type'      => $item['item_type'],                // tipo: suministro o pack
        'id'             => $item['item_id'],                  // id
        'name'           => $item['item_name'],                // nombre del suministro o pack
        'description'    => $item['item_desc'],                // descripcion corta
        'trademark'      => $trademark,                        // marca comercial
        'type'           => $type,                             // insumo o ingrediente
        'unit_volume'    => $unit_volume,                      // kg, L, g, mL, ... unitario
        'item_count'     => (int) $item['item_quantity'],      // cantidad comprada
        'total_volume'   => $total_volume,                     // volumen unitario * cantidad comprada
        'unit_price'     => (float) $item['item_unit_price'],  // precio unitario
        'subtotal_price' => (float) $item['item_total_price']  // precio total
      ];
    });

    // Separar items por tipo
    $provisions = $items->where('item_type', 'provision')->values();
    $packs      = $items->where('item_type', 'pack')->values();

    return [
      'supplier'                => $supplier,               // proveedor
      'purchase_date'           => '',                      // fecha de compra
      'total_price'             => $order_total,            // costo total
      'purchase_reference_id'   => $preorder->id,           // id de preorden referenciada
      'purchase_reference_type' => get_class($preorder),    // modelo de preorden (clase)
      'order_code'              => $order_code,             // codigo de orden (orden definitiva)
      'order_date'              => $order_date,             // fecha de orden
      'provisions'              => $provisions,             // coleccion de suministros
      'packs'                   => $packs                   // coleccion de packs
    ];
  }

  /**
   * Crear una nueva compra con sus items y existencias
   * @param array $purchase_data datos de compra
   * @param Collection $items coleccion de items que se compraran
   * @return Purchase
   */
  public function createPurchase(array $purchase_data, Collection $purchase_items): Purchase
  {
    try {
      DB::beginTransaction();

      // 1. Crear la compra
      $purchase = Purchase::create([
        'supplier_id'             => $purchase_data['supplier']->id,
        'purchase_date'           => $purchase_data['purchase_date'],
        'total_price'             => $purchase_items->sum('subtotal_price'),
        'purchase_reference_id'   => $purchase_data['purchase_reference_id'] ?? null,
        'purchase_reference_type' => $purchase_data['purchase_reference_type'] ?? null,
      ]);

      // 2. Crear detalles de compra y existencias
      foreach ($purchase_items as $item) {

        // detalle de compra
        $detail = $this->createPurchaseDetail($purchase->id, $item);

        // existencia por compra
        $this->createExistence($purchase->id, $detail);
      }

      DB::commit();
      return $purchase;

    } catch (\Exception $e) {

      DB::rollBack();
      throw $e;
    }
  }

  /**
   * Crear detalle de compra
   * @param $purchase_id id de compra
   * @param array $item un item que debe crearse como detalle
   * @return PurchaseDetail
   */
  private function createPurchaseDetail(int $purchase_id, array $item): PurchaseDetail
  {
    return PurchaseDetail::create([
      'purchase_id'     => $purchase_id,
      'provision_id'    => $item['item_type'] === 'provision' ? $item['id'] : null,
      'pack_id'         => $item['item_type'] === 'pack' ? $item['id'] : null,
      'item_count'      => $item['item_count'], // cantidad comprada
      'unit_price'      => $item['unit_price'],
      'subtotal_price'  => $item['subtotal_price']
    ]);
  }

  /**
   * Crear registro de existencias
   * @param int $purchase_id id de compra
   * @param PurchaseDetail $detail un renglon detalle (suministro o pack)
   */
  private function createExistence(int $purchase_id, PurchaseDetail $detail): void
  {
    // obtenemos el id del suministro para el cual aumenta su existencia
    $provision = $detail->provision_id
      ? Provision::find($detail->provision_id)
      : Pack::find($detail->pack_id)->provision;

    // cantidad total comprada = total items * volumen unitario
    // * las existencias siempre se almacenan con una cantidad float en la unidad (Kg, L, U, M)
    $quantity_amount = $detail->item_count * $provision->provision_quantity; // 4 * 1.5

    Existence::create([
      'provision_id'    => $provision->id,
      'purchase_id'     => $purchase_id,
      'movement_type'   => Existence::MOVEMENT_TYPE_COMPRA(),
      'registered_at'   => now(),
      'quantity_amount' => (float) $quantity_amount,
    ]);
  }

  /**
   * obtener una referencia a la preorden de la compra
   * @param Purchase $purchase
   */
  public function getPreorderReference(Purchase $purchase)
  {
    if ($purchase->purchase_reference_id !== null && $purchase->purchase_reference_type !== null) {

      $preorder = PreOrder::find($purchase->purchase_reference_id);
      $order_data = json_decode($preorder->order, true);

      return [
        'id' => $preorder->id,
        'order_code' => $order_data['order_code'],
        'order_date' => $order_data['order_date']
      ];

    }

    return false;
  }
}
