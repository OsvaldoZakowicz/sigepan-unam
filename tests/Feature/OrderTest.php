<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Product;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
  use RefreshDatabase;

  public $user_data = [
    'name' => 'Jhon Doe',
    'email' => 'joooooh@gmail.com',
    'password' => '12345678',
  ];

  public $employee_data = [
    'name' => 'Peter Parker',
    'email' => 'spiderboss@gmail.com',
    'password' => '12345678',
  ];

  public $order_status_data = [
    'status' => 'pendiente',
  ];

  public $order_data = [
    'order_code' => 'ORD-0001',
    'order_status_id' => null,
    'user_id' => null,
    'employee_id' => null,
    'order_origin' => 'web',
    'total_price' => 100.00,
    'delivered_at' => '2021-10-01 00:00:00',
  ];

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_price' => 4500,
    'product_short_description' => 'lorem ipsup description piola',
    'product_expires_in' => 2,
    'product_in_store' => true,
  ];

  public $sale_data = [
    'order_id'            => null, // id de la orden o pedido de compra
    'payment_type'        => "mercado pago", // tipo de pago: efectivo, mercado pago, tarjeta, etc
    'payment_id'          => "102720537383", // numero de transaccion MP
    'status'              => "approved", // estado del pago MP
    'external_reference'  => "10", // referencia externa al pago MP
    'merchant_order_id'   => null, // MP
    'total_price'         => 100.00, // precio total de la venta
    'full_response'       => "{}",// respuesta completa de MP (u otro medio de pago)
  ];

  /**
   * test crear una orden via WEB
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
   * test crear orden presencial
   * @return void
   */
  public function test_crear_orden_presencial(): void
  {
    $client = User::create($this->user_data);
    $employee = User::create($this->employee_data);
    $status = OrderStatus::create($this->order_status_data);

    $this->order_data['user_id'] = $client->id;
    $this->order_data['employee_id'] = $employee->id;
    $this->order_data['order_status_id'] = $status->id;
    $this->order_data['order_origin'] = 'presencial';

    $order = Order::create($this->order_data);

    $this->assertInstanceOf(Order::class, $order);
    $this->assertDatabaseHas('orders', $this->order_data);
  }

  /**
   * test una orden tiene un estado de orden
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
   * test una orden tiene productos
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
      'quantity' => 2,
      'unit_price' => 4500,
      'subtotal_price' => 9000,
    ]);

    $this->assertInstanceOf(BelongsToMany::class, $order->products());
    $this->assertDatabaseHas('order_product', [
      'order_id' => $order->id,
      'product_id' => $product->id,
      'quantity' => 2,
      'unit_price' => 4500,
      'subtotal_price' => 9000,
    ]);
  }

  /**
   * test una orden tiene una venta asociada
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
