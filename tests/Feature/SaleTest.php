<?php

namespace Tests\Feature;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

class SaleTest extends TestCase
{

    use RefreshDatabase;
    use WithoutModelEvents;

    public $user_data = [
        'name' => 'Jhon Doe',
        'email' => 'joooooh@gmail.com',
        'password' => '12345678',
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
     * @purpose Crear una venta de productos para un usuario.
     * @expectedResult Se crea una venta en el sistema.
     * @observations Ninguna.
     * @return void
     */
    public function test_crear_venta_asociada_a_usuario_y_producto(): void
    {
        $client = \App\Models\User::create($this->user_data);

        $this->sale_data['user_id'] = $client->id;
        $sale = \App\Models\Sale::create($this->sale_data);

        $product = \App\Models\Product::create($this->product_data);

        $sale->products()->attach($product->id, [
            'sale_quantity' => 2,
            'unit_price' => 4500,
            'subtotal_price' => 9000,
            'details' => 'detalle',
        ]);

        $this->assertInstanceOf(BelongsToMany::class, $sale->products());
        $this->assertDatabaseHas('product_sale', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'sale_quantity' => 2,
            'unit_price' => 4500,
            'subtotal_price' => 9000,
            'details' => 'detalle',
        ]);
    }
}
