<?php

namespace App\Services\Sale;

use App\Models\DatoNegocio;
use App\Models\Order;
use App\Models\OrderStatus;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
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
   */

  /**
   * crear preferencia de pago.
   * @param array $cart carrito de compras
   * @return array datos de preferencia o error
   */
  public function createMercadoPagoPreference($cart): array
  {
    try {
      // configuración token de acceso
      MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

      // Preparar los items del carrito de compras
      $items = [];
      foreach ($cart as $cart_item) {
        $items[] = [
          "title"       => $cart_item['product']->product_name,
          "description" => $cart_item['details'],
          "quantity"    => (int)$cart_item['order_quantity'],
          "unit_price"  => (float)$cart_item['unit_price']
        ];
      }

      // crear el cliente de preferencia
      $client = new PreferenceClient();

      // obtener URL base de ngrok desde el archivo .env
      // para configurar el retorno desde mercado pago
      $base_url = env('NGROK_URL', env('APP_URL'));

      // referencia externa, codigo de orden
      $external_reference = $this->generateUniqueOrderCode();

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
