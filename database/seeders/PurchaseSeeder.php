<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Provision;
use App\Models\ProvisionCategory;
use App\Models\ProvisionTrademark;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Existence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $proveedores_ingredientes = [
      'proveedor1' => Supplier::where('company_name', 'molinos rio de la plata sa')->first(),
      'proveedor2' => Supplier::where('company_name', 'arcor saic')->first(),
      'proveedor3' => Supplier::where('company_name', 'la serenisima sa')->first(),
    ];

    $proveedores_insumos = [
      'proveedor4' => Supplier::where('company_name', 'distribuidora del norte srl')->first(),
    ];

    $suministros_ingrediente = Provision::with('provision_category')
      ->whereHas('type', function ($query) {
        $query->where('provision_type_name', 'ingrediente');
      })->get();

    $suministros_insumo = Provision::whereHas('type', function ($query) {
      $query->where('provision_type_name', 'insumo');
    })->get();

    // lista de compras
    $shopping_list = [
      'harina_000' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'harina 000')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name'     => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity_variant' => 25, // variante en la cantidad
            'quantity_to_buy'  => 4,    // cantidad a comprar
          ],
          'pureza' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity_variant' => 5, // variante en la cantidad
            'quantity_to_buy'  => 6,   // cantidad a comprar
          ],
        ],
      ],
      'harina_0000' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'harina 0000')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name'     => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity_variant' => 25, // variante en la cantidad
            'quantity_to_buy'  => 4,    // cantidad a comprar
          ],
          'pureza' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity_variant' => 5, // variante en la cantidad
            'quantity_to_buy'  => 6,   // cantidad a comprar
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity_variant' => 25, // variante en la cantidad
            'quantity_to_buy'  => 2,    // cantidad a comprar
          ],
        ]
      ],
      'azucar' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'azucar')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'ledesma' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'ledesma')->first(),
            'quantity_variant' => 1, // variante en la cantidad
            'quantity_to_buy'  => 40,  // cantidad a comprar
          ],
          'la muñeca' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'la muñeca')->first(),
            'quantity_variant' => 1, // variante en la cantidad
            'quantity_to_buy'  => 30,  // cantidad a comprar
          ],
        ],
      ],
      'aceite_girasol'  => [
        'category'   => ProvisionCategory::where('provision_category_name', 'aceite girasol')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity_variant' => 4.5, // variante en la cantidad
            'quantity_to_buy'  => 10,  // cantidad a comprar
          ],
          'natura'  => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'natura')->first(),
            'quantity_variant' => 1.5, // variante en la cantidad
            'quantity_to_buy'  => 10,  // cantidad a comprar
          ],
        ],
      ],
      'levadura_humeda' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'levadura humeda')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'pureza' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity_variant' => 0.05,
            'quantity_to_buy'  => 200,
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity_variant'  => 0.05,
            'quantity_to_buy'   => 100,
          ],
        ],
      ],
      'dulce_membrillo' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'dulce de membrillo')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity_variant' => 0.5,
            'quantity_to_buy'  => 30
          ],
          'arcor' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'arcor')->first(),
            'quantity_variant' => 5,
            'quantity_to_buy'  => 15
          ],
        ],
      ],
      'dulce_batata' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'dulce de batata')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity_variant' => 0.5,
            'quantity_to_buy'  => 30
          ],
          'arcor' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'arcor')->first(),
            'quantity_variant' => 5,
            'quantity_to_buy'  => 15
          ],
        ],
      ],
      'dulce_leche' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'dulce de leche')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'la serenísima' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'la serenísima')->first(),
            'quantity_variant' => 0.25,
            'quantity_to_buy'  => 30,
          ],
          'milkaut' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'milkaut')->first(),
            'quantity_variant' => 1,
            'quantity_to_buy'  => 30,
          ],
        ],
      ],
      'sal_entrefina' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'sal entrefina')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'dos anclas' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'dos anclas')->first(),
            'quantity_variant' => 1,
            'quantity_to_buy'  => 10,
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity_variant' => 1,
            'quantity_to_buy'  => 10,
          ],
        ],
      ],
      'bolsa_plast_10'  => [
        'category'   => ProvisionCategory::where('provision_category_name', 'bolsa plastica numero 10')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'inapack' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'inapack')->first(),
            'quantity_variant' => 1,
            'quantity_to_buy'  => 250,
          ],
          'empacar' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'empacar')->first(),
            'quantity_variant' => 1,
            'quantity_to_buy'  => 250,
          ],
        ],
      ],
      'bolsa_papel_10'  => [
        'category'   => ProvisionCategory::where('provision_category_name', 'bolsa de papel numero 10')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'inapack' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'inapack')->first(),
            'quantity_variant'   => 1,
            'quantity_to_buy'  => 250,
          ],
          'empacar' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'empacar')->first(),
            'quantity_variant' => 1,
            'quantity_to_buy'  => 250,
          ],
        ],
      ],
    ];

    // * para cada proveedor de ingredientes
    foreach ($proveedores_ingredientes as $proveedor) {
      $detalles_compra = [];
      $total_compra = 0;

      // Buscar suministros que puede proveer de la lista
      foreach ($shopping_list as $categoria) {
        foreach ($categoria['trademarks'] as $marca) {
          // Buscar el suministro específico (cantidad y marca)
          $suministro = $suministros_ingrediente
            ->where('provision_category_id', $categoria['category']->id)
            ->where('provision_trademark_id', $marca['name']->id)
            ->where('provision_quantity', $marca['quantity_variant'])
            ->first();

          if (!$suministro) continue;

          // Verificar si el proveedor tiene este suministro
          $precio = $proveedor->provisions()
            ->where('provision_id', $suministro->id)
            ->first();

          if (!$precio) continue;

          // Calcular subtotal
          $subtotal = $precio->pivot->price * $marca['quantity_to_buy'];
          $total_compra += $subtotal;

          // Agregar detalle
          $detalles_compra[] = [
            'suministro' => $suministro,
            'cantidad' => $marca['quantity_to_buy'],
            'precio_unitario' => $precio->pivot->price,
            'subtotal' => $subtotal
          ];
        }
      }

      // Si el proveedor tiene items para vender
      if (count($detalles_compra) > 0) {
        // Crear la compra
        $compra = Purchase::create([
          'supplier_id' => $proveedor->id,
          'purchase_date' => now(),
          'total_price' => $total_compra,
          'status' => 'completada'
        ]);

        // Crear detalles y existencias
        foreach ($detalles_compra as $detalle) {
          // Crear detalle de compra
          $purchase_detail = PurchaseDetail::create([
            'purchase_id' => $compra->id,
            'provision_id' => $detalle['suministro']->id,
            'item_count' => $detalle['cantidad'],
            'unit_price' => $detalle['precio_unitario'],
            'subtotal_price' => $detalle['subtotal']
          ]);

          // Crear registro de existencias
          Existence::create([
            'provision_id' => $detalle['suministro']->id,
            'purchase_id' => $compra->id,
            'stock_id' => null,
            'movement_type' => Existence::MOVEMENT_TYPE_COMPRA(),
            'registered_at' => now(),
            'quantity_amount' => $detalle['cantidad'] * $detalle['suministro']->provision_quantity
          ]);
        }
      }
    }

    // * para cada proveedor de insumos
    foreach ($proveedores_insumos as $proveedor) {
      $detalles_compra = [];
      $total_compra = 0;

      // Buscar insumos que puede proveer de la lista
      foreach ($shopping_list as $categoria) {
        // Verificar que sea un insumo
        if ($categoria['category']->provision_type->provision_type_name !== 'insumo') {
          continue;
        }

        foreach ($categoria['trademarks'] as $marca) {
          // Buscar el insumo específico (cantidad y marca)
          $suministro = $suministros_insumo
            ->where('provision_category_id', $categoria['category']->id)
            ->where('provision_trademark_id', $marca['name']->id)
            ->where('provision_quantity', $marca['quantity_variant'])
            ->first();

          if (!$suministro) continue;

          // Verificar si el proveedor tiene este insumo
          $precio = $proveedor->provisions()
            ->where('provision_id', $suministro->id)
            ->first();

          if (!$precio) continue;

          // Calcular subtotal
          $subtotal = $precio->pivot->price * $marca['quantity_to_buy'];
          $total_compra += $subtotal;

          // Agregar detalle
          $detalles_compra[] = [
            'suministro' => $suministro,
            'cantidad' => $marca['quantity_to_buy'],
            'precio_unitario' => $precio->pivot->price,
            'subtotal' => $subtotal
          ];
        }
      }

      // Si el proveedor tiene items para vender
      if (count($detalles_compra) > 0) {
        // Crear la compra
        $compra = Purchase::create([
          'supplier_id' => $proveedor->id,
          'purchase_date' => now(),
          'total_price' => $total_compra,
          'status' => 'completada'
        ]);

        // Crear detalles y existencias
        foreach ($detalles_compra as $detalle) {
          // Crear detalle de compra
          $purchase_detail = PurchaseDetail::create([
            'purchase_id' => $compra->id,
            'provision_id' => $detalle['suministro']->id,
            'item_count' => $detalle['cantidad'],
            'unit_price' => $detalle['precio_unitario'],
            'subtotal_price' => $detalle['subtotal']
          ]);

          // Crear registro de existencias
          Existence::create([
            'provision_id' => $detalle['suministro']->id,
            'purchase_id' => $compra->id,
            'stock_id' => null,
            'movement_type' => Existence::MOVEMENT_TYPE_COMPRA(),
            'registered_at' => now(),
            'quantity_amount' => $detalle['cantidad'] * $detalle['suministro']->provision_quantity
          ]);
        }
      }
    }
  }
}
