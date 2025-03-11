<?php

namespace App\Services\Supplier;

use App\Models\Pack;
use App\Models\PreOrder;
use App\Models\Provision;
use App\Models\Quotation;
use App\Models\Supplier;
use Illuminate\Support\Collection;

/**
 * servicios para pre ordenes y ordenes
 */
class PreOrderService
{

  /**
   * generar datos finales de la orden de compra
   * todo: Datos de panaderia
   * @param PreOrder $preorder
   * @param Quotation $quotation
   * @param Collection $preorder_items conjunto de suministros y/o packs
   * @return array $order_data
   */
  public function generateOrderData(PreOrder $preorder, Quotation $quotation, Collection $preorder_items): array
  {
    $body_order_data = $this->generateBodyOrderData($preorder_items);

    $anexo = json_decode($preorder->details, true);

    $order_data = [
      'code'            =>  (string) substr($preorder->pre_order_code, 3),
      'date'            =>  (string) now()->format('d-m-Y'), // fecha de la orden
      'budget_date'     =>  (string) ($quotation) ? formatDateTime($quotation->created_at, 'd-m-Y') : '-',  // fecha de el presupuesto previo
      'issuer_name'     =>  (string) 'Panadería',
      'issuer_cuit'     =>  (string) '99999999999',
      'issuer_email'    =>  (string) 'Email@Ejemplo.Com',
      'issuer_phone'    =>  (string) '3758252525',
      'provider_name'   =>  (string) $preorder->supplier->company_name,
      'provider_cuit'   =>  (string) $preorder->supplier->company_cuit,
      'provider_email'  =>  (string) $preorder->supplier->user->email,
      'provider_phone'  =>  (string) $preorder->supplier->phone_number,
      'items'           =>  $body_order_data['body_items'],
      'total'           =>  $body_order_data['body_total_price'],
      'anexo'           =>  $anexo,
    ];

    return $this->utf8EncodeArray($order_data);
  }

  /**
   * generar datos del cuerpo para la orden
   * @param Collection $preorder_items conjunto de suministros y/o packs
   * @return array $body_order_data = ['body_items' => [], 'body_total_price' => '...']
   */
  private function generateBodyOrderData(Collection $preorder_items): array
  {
    // Filtrar solo los items que tienen stock o cantidad alternativa
    $purchasable_items = $preorder_items->filter(function ($item) {
      return $item['item_has_stock'] ||
        (!$item['item_has_stock'] && $item['item_alternative_quantity'] > 0);
    });

    $body_items = $purchasable_items->map(function ($item) {
      return [
        'item_id'           =>  (string) $item['item_id'],
        'item_name'         =>  (string) $this->getItemName($item['item_object']),
        'item_desc'         =>  (string) $this->getItemTrademarkAndVolume($item['item_object']),
        'item_type'         =>  (string) $item['item_type'],
        'item_quantity'     =>  (string) $this->getItemQuantity($item),
        'item_unit_price'   =>  (string) $item['item_unit_price'],
        'item_total_price'  =>  (string) $item['item_total_price'],
      ];
    });

    $body_total_price = $body_items->reduce(function ($acc, $bi) {
      return $acc + $bi['item_total_price'];
    });

    $body_order_data = [];
    $body_order_data['body_items'] = $body_items->toArray();
    $body_order_data['body_total_price'] = (string) $body_total_price;

    return $body_order_data;
  }

  /**
   * obtener nombre del suministro o pack
   * @param Provision | Pack $object
   * @return string name
   */
  private function getItemName($object): string
  {
    $name = '';

    if ($object instanceof Provision) {
      $name = $object->provision_name;
    }

    if ($object instanceof Pack) {
      $name = $object->pack_name;
    }

    return $name;
  }

  /**
   * obtener marca y volumen
   * @param Provision | Pack $object
   * @return string marca y volumen
   */
  private function getItemTrademarkAndVolume($object): string
  {
    $trademark_volume = '';

    if ($object instanceof Provision) {
      $trademark_volume = $object->trademark->provision_trademark_name
        . ' ' . convert_measure($object->provision_quantity, $object->measure);
    }

    if ($object instanceof Pack) {
      $trademark_volume = $object->provision->trademark->provision_trademark_name
        . ' ' . convert_measure($object->pack_quantity, $object->provision->measure);
    }

    return $trademark_volume;
  }

  /**
   * definir cantidad del item
   * @param $item
   * @return string cantidad
   */
  private function getItemQuantity($item): string
  {
    $quantity = '';

    if ($item['item_alternative_quantity'] > 0) {
      $quantity = $item['item_alternative_quantity'];
    } else {
      $quantity = $item['item_quantity'];
    }

    return $quantity;
  }

  /**
   * Función para asegurar que todos los strings en un array estén en UTF-8
   * @param array $array
   * @return array
   */
  private function utf8EncodeArray(array $array): array
  {
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $array[$key] = $this->utf8EncodeArray($value);
      } elseif (is_string($value)) {
        $array[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
      }
    }
    return $array;
  }
}
