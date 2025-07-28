<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Product;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
  use RefreshDatabase;
  use WithoutModelEvents;

  public $user_data = [
    'name' => 'Jhon Doe',
    'email' => 'joooooh@gmail.com',
    'password' => '12345678',
  ];

  public $order_status_data = [
    'status' => 'pendiente',
  ];

  public $order_data = [
    'order_code' => 'ORD-0001',
    'order_status_id' => null,
    'user_id' => null,
    'total_price' => 100.00,
    'ordered_at' => '2021-10-01 00:00:00',
    'delivered_at' => '2021-10-01 00:00:00',
    'payment_status' => 'pendiente'
  ];

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_price' => 4500,
    'product_short_description' => 'lorem ipsup description piola',
    'product_expires_in' => 2,
    'product_in_store' => true,
  ];

  public $sale_data = [
    'order_id' => null, // id de la orden o pedido de compra (nullable)
    'user_id' => null,  // id del usuario que compra (nullable)
    'client_type' => 'cliente registrado',      // tipo de cliente: cliente registrado o cliente no registrado
    'sale_type' => 'venta presencial',        // tipo de venta: web o presencial
    'sold_on' => '2021-10-01 00:00:00', // fecha de la venta
    'payment_type' => 'efectivo', // tipo de pago: efectivo, mercado pago, tarjeta, etc
    'payment_id' => null,         // !numero de transaccion (null)
    'status' => null,             // !estado del pago MP (null)
    'external_reference' => null, // !referencia externa al pago MP (null)
    'merchant_order_id' => null,  // !MP (null)
    'total_price' => 100,         // precio total de la venta
    'full_response' => null,      // ?respuesta completa de MP (u otro medio de pago) (null, completar solo esto)
    'sale_pdf_path' => null,      // ruta al pdf comprobante de venta// respuesta completa de MP (u otro medio de pago)
  ];

  /**
   * @testCase TC001.
   * @purpose Crear una orden (pedido) de productos a traves de la tienda web.
   * @expectedResult Se crea una orden en el sistema.
   * @observations Ninguna.
   * @return void
  */
  public function test_crear_orden_via_web(): void
  {
    $client = User::create($this->user_data);
    $status = OrderStatus::create($this->order_status_data);

    $this->order_data['user_id'] = $client->id;
    $this->order_data['order_status_id'] = $status->id;

    $order = Order::create($this->order_data);

    $this->assertInstanceOf(Order::class, $order);
    $this->assertDatabaseHas('orders', $this->order_data);
  }

  /**
   * @testCase TC002.
   * @purpose Una orden tiene un estado de orden.
   * @expectedResult Se verifica que una orden tiene un estado asociado.
   * @observations Ninguna.
   * @return void
   */
  public function test_una_orden_tiene_un_estado(): void
  {
    $client = User::create($this->user_data);
    $status = OrderStatus::create($this->order_status_data);

    $this->order_data['user_id'] = $client->id;
    $this->order_data['order_status_id'] = $status->id;

    $order = Order::create($this->order_data);

    $this->assertInstanceOf(BelongsTo::class, $order->status());
  }

  /**
   * @testCase TC003.
   * @purpose Una orden tiene productos asociados.
   * @expectedResult Se verifica que una orden tiene un productos asociados.
   * @observations Ninguna.
   * @return void
   */
  public function test_una_orden_tiene_productos(): void
  {
    $client = User::create($this->user_data);
    $status = OrderStatus::create($this->order_status_data);

    $this->order_data['user_id'] = $client->id;
    $this->order_data['order_status_id'] = $status->id;

    $order = Order::create($this->order_data);
    $product = Product::create($this->product_data);

    $order->products()->attach($product->id, [
      'order_quantity' => 2,
      'unit_price' => 4500,
      'subtotal_price' => 9000,
      'details' => 'detalle',
    ]);

    $this->assertInstanceOf(BelongsToMany::class, $order->products());
    $this->assertDatabaseHas('order_product', [
      'order_id' => $order->id,
      'product_id' => $product->id,
      'order_quantity' => 2,
      'unit_price' => 4500,
      'subtotal_price' => 9000,
      'details' => 'detalle',
    ]);
  }

  /**
   * @testCase TC004.
   * @purpose Una orden tiene una venta asociada.
   * @expectedResult Se verifica que una orden tiene una venta asociada.
   * @observations Una orden podria no tener venta asociada si se cancela antes del pago.
   * @return void
   */
  public function test_una_orden_tiene_una_venta(): void
  {
    $client = User::create($this->user_data);
    $status = OrderStatus::create($this->order_status_data);

    $this->order_data['user_id'] = $client->id;
    $this->order_data['order_status_id'] = $status->id;

    $order = Order::create($this->order_data);

    $this->sale_data['order_id'] = $order->id;
    $sale = Sale::create($this->sale_data);

    $this->assertInstanceOf(Sale::class, $sale);
    $this->assertInstanceOf(HasOne::class, $order->sale());
  }
}
