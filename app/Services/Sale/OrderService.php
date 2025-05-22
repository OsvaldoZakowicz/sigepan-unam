<?php

namespace App\Services\Sale;

use App\Models\DatoNegocio;
use App\Models\Order;
use App\Models\OrderStatus;
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
   * todo: crear orden
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

      // vincular los productos a la orden
      foreach ($cart as $cart_item) {
        $order->products()->attach($cart_item['product']->id, [
          'order_quantity' => $cart_item['order_quantity'],
          'unit_price' => $cart_item['unit_price'],
          'subtotal_price' => $cart_item['subtotal_price'],
          'details' => $cart_item['details']
        ]);
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
}
