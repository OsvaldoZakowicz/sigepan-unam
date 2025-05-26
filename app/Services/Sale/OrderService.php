<?php

namespace App\Services\Sale;

use App\Models\DatoNegocio;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\Stock\StockService;
use App\Models\Stock;
use App\Models\StockMovement;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{

  /**
   * prefijo para codigo de orden
   */
  protected static $ORDER_PREFIX = 'ORD_';

  /**
   * obtener prefijo para codigo de orden
   * @return string
   */
  public static function ORDER_PREFIX(): string
  {
    return self::$ORDER_PREFIX;
  }

  /**
   * crear codigos de preorden unicos
   * @return string
   */
  private function generateUniqueOrderCode(): string
  {
    // Conjunto de caracteres para generar el código aleatorio restante
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz#$&@?!';
    $characters_length = strlen($characters);

    // Generar el resto del codigo (longitud total sera 12 + longitud del prefijo)
    $random_part = '';
    for ($i = 0; $i < 12; $i++) {
      $random_part .= $characters[random_int(0, $characters_length - 1)];
    }

    // Combinar prefijo con parte aleatoria
    $code = $this->ORDER_PREFIX() . $random_part;

    // Verificar unicidad
    while ($this->codeExists($code)) {
      $random_part = '';
      for ($i = 0; $i < 10; $i++) {
        $random_part .= $characters[random_int(0, $characters_length - 1)];
      }
      $code = $this->ORDER_PREFIX() . $random_part;
    }

    return $code;
  }

  /**
   * metodo auxiliar para verificar la unicidad del codigo
   * verifica que no exista un codigo similar en la base de datos
   * @param string $code codigo a verificar
   * @return bool existencia o no del codigo
   */
  private function codeExists(string $code): bool
  {
    return Order::where('order_code', $code)->exists();
  }

  /**
   * crear orden (o pedido) de productos desde la tienda online
   * @param array $cart carrito de compras
   * @param User $user usuario autenticado en la tienda
   * @param float $total de la compra
   * @return Order $order
   */
  public function createOrder($cart, $user, $total): Order
  {

    try {

      // codigo de orden
      $new_order_code = $this->generateUniqueOrderCode();

      // estado de entrega de productos pendiente
      $new_order_status = OrderStatus::ORDER_STATUS_PENDIENTE();

      // estado de pago pendiente
      $new_order_payment_status = Order::ORDER_PAYMENT_STATUS_PENDIENTE();

      DB::beginTransaction();

      // crear orden
      $order = Order::create([
        'order_code'      => $new_order_code,
        'order_status_id' => $new_order_status,
        'user_id'         => $user->id,
        'total_price'     => $total,
        'ordered_at'      => now()->format('Y-m-d H:i:s'),
        'delivered_at'    => null,
        'payment_status'  => $new_order_payment_status,
      ]);

      // instanciar servicio de stock
      $stock_service = new StockService();

      // vincular los productos a la orden y reducir stock
      foreach ($cart as $cart_item) {

        // Vincular producto con la orden
        $order->products()->attach($cart_item['product']->id, [
          'order_quantity' => $cart_item['order_quantity'],
          'unit_price' => $cart_item['unit_price'],
          'subtotal_price' => $cart_item['subtotal_price'],
          'details' => $cart_item['details']
        ]);

        // Extraer la cantidad del detalle (entre parentesis)
        preg_match('/\((\d+)\)/', $cart_item['details'], $matches);
        $units_per_item = (int)$matches[1];
        $total_units_to_deduct = $units_per_item * $cart_item['order_quantity'];

        // Obtener stocks disponibles ordenados por fecha de vencimiento
        $available_stocks = Stock::where('product_id', $cart_item['product']->id)
          ->where('quantity_left', '>', 0)
          ->orderBy('expired_at')
          ->get();

        $remaining_units = $total_units_to_deduct;

        foreach ($available_stocks as $stock) {
          if ($remaining_units <= 0) break;

          // Calcular cuántas unidades podemos tomar de este stock
          $units_to_deduct = min($remaining_units, $stock->quantity_left);

          // Registrar movimiento negativo
          $stock_service->registerMovement(
            $stock->id,
            -$units_to_deduct,
            StockMovement::MOVEMENT_TYPE_PEDIDO(),
            $order->id,
            get_class($order)
          );

          $remaining_units -= $units_to_deduct;
        }

        // Si quedaron unidades sin descontar, no hay suficiente stock
        if ($remaining_units > 0) {
          throw new \Exception(
            "Stock insuficiente para el producto {$cart_item['product']->product_name}. " .
              "Faltan {$remaining_units} unidades."
          );
        }
      }

      DB::commit();
      return $order;
    } catch (\Exception $e) {

      DB::rollBack();
      throw $e;
    }
  }

  /**
   * crear preferencia de pago.
   * @param $order orden de compra
   * @return array datos de preferencia o error
   */
  public function createMercadoPagoPreference($order): array
  {
    try {
      // configuración token de acceso
      MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

      // Preparar los items del carrito de compras
      $items = [];
      foreach ($order->products as $product) {
        $items[] = [
          "title"       => $product->product_name,
          "description" => $product->pivot->details,
          "quantity"    => (int) $product->pivot->order_quantity,
          "unit_price"  => (float) $product->pivot->unit_price
        ];
      }

      // crear el cliente de preferencia
      $client = new PreferenceClient();

      // obtener URL base de ngrok desde el archivo .env
      // para configurar el retorno desde mercado pago
      $base_url = env('NGROK_URL', env('APP_URL'));

      // referencia externa, codigo de orden
      $external_reference = $order->order_code;

      // descriptor del nombre del negocio
      $statement_descriptor = DatoNegocio::obtenerValor('razon_social');

      $preference = $client->create([
        "statement_descriptor" => $statement_descriptor,
        "external_reference" => $external_reference,
        "items" => $items,
        "auto_return" => "approved",
        "back_urls" => [
          "success" => $base_url . route('store-store-payment-success', [], false),
          "failure" => $base_url . route('store-store-payment-failure', [], false),
          "pending" => $base_url . route('store-store-payment-pending', [], false)
        ],
      ]);

      return [
        'preference_id' => $preference->id, // id de la preferencia para inicializar el boton de MP
        'init_point'    => $preference->init_point // ruta al pago en MP
      ];
    } catch (MPApiException $e) {

      // Manejo específico de errores de la API de Mercado Pago
      Log::error('Error en Mercado Pago API: ' . $e->getMessage(), [
        'code' => $e->getCode(),
        'response' => $e->getApiResponse(),
      ]);

      return ['error' => 'Error al procesar el pago: ' . $e->getMessage()];
    } catch (Exception $e) {

      // Manejo de otros errores
      Log::error('Error al crear preferencia de pago: ' . $e->getMessage(), [
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
      ]);

      return ['error' => 'Error al procesar el pago. Por favor, intente nuevamente.'];
    }
  }

  /**
   * cancelar un pedido (u orden)
   * una orden puede cancelarse si su estado de pago es pendiente y su
   * entrega de pedido es pendiente, en otros casos no.
   * @param int $id id de orden
   * @return array resultado de la operación
   */
  public function cancelOrder(int $id): array
  {
    try {
      DB::beginTransaction();

      // buscar la orden
      $order = Order::findOrFail($id);

      // verificar que sea cancelable
      if (
        $order->payment_status !== Order::ORDER_PAYMENT_STATUS_PENDIENTE() ||
        $order->order_status_id !== OrderStatus::ORDER_STATUS_PENDIENTE()
      ) {
        return [
          'success' => false,
          'message' => 'El pedido no puede ser cancelado porque ya fue pagado o entregado'
        ];
      }

      // Obtener todos los movimientos de stock asociados a esta orden
      $stock_movements = StockMovement::where('movement_reference_id', $order->id)
        ->where('movement_reference_type', get_class($order))
        ->get();

      $stock_service = new StockService();

      // Revertir cada movimiento
      foreach ($stock_movements as $movement) {
        // Como el movimiento original fue negativo (salida),
        // aquí usamos la cantidad positiva para revertirlo
        $stock_service->registerMovement(
          $movement->stock_id,
          abs($movement->quantity), // convertimos a positivo
          StockMovement::MOVEMENT_TYPE_PEDIDO_CANCELADO(),
          $order->id,
          get_class($order)
        );
      }

      // actualizar estado a cancelado
      $order->payment_status = Order::ORDER_PAYMENT_STATUS_RECHAZADO();
      $order->order_status_id = OrderStatus::ORDER_STATUS_CANCELADO();
      $order->save();

      DB::commit();

      return [
        'success' => true,
        'message' => 'Pedido cancelado exitosamente'
      ];
    } catch (\Exception $e) {
      DB::rollBack();

      return [
        'success' => false,
        'message' => 'Error al cancelar el pedido: ' . $e->getMessage()
      ];
    }
  }

  /**
   * entregar un pedido (u orden)
   * una orden puede entregarse si su estado de pago es aprobado
   * y su entrega de pedido es pendiente
   * @param int $id id dde orden
   * @return array resultado de la operacion
   */
  public function entregaOrder(int $id): array
  {
    try {
      DB::beginTransaction();

      // buscar la orden
      $order = Order::findOrFail($id);

      // verificar que sea entregable
      if (
        $order->payment_status !== Order::ORDER_PAYMENT_STATUS_APROBADO() ||
        $order->order_status_id !== OrderStatus::ORDER_STATUS_PENDIENTE()
      ) {
        return [
          'success' => false,
          'message' => 'El pedido no puede ser entregado porque adeuda el pago, o ya fue entregado antes'
        ];
      }

      // actualizar estado de orden a entregado y establecer fecha de entrega actual
      $order->order_status_id = OrderStatus::ORDER_STATUS_ENTREGADO();
      $order->delivered_at = now()->format('d-m-Y H:i');
      $order->save();

      DB::commit();

      return [
        'success' => true,
        'message' => 'Pedido entregado exitosamente'
      ];
    } catch (\Exception $e) {
      DB::rollBack();

      return [
        'success' => false,
        'message' => 'Error al entregar el pedido: ' . $e->getMessage()
      ];
    }
  }
}
