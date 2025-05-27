<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Provision;
use App\Models\ProvisionCategory;
use App\Models\ProvisionTrademark;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricesSeeder extends Seeder
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

    // categorias con marcas, variantes y precios por variante
    $provision_categories = [
      'harina_000' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'harina 000')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name'     => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
            'prices_by_quantity' => [
              [750, 900],
              [5200.35, 6500.85],
              [12600.30, 14520.35]
            ], // rango de precios por cantidad
          ],
          'pureza' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
            'prices_by_quantity' => [
              [830, 900],
              [5600.35, 6500.85],
              [13600.30, 14520.35]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'harina_0000' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'harina 0000')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
            'prices_by_quantity' => [
              [850, 1100],
              [7200.35, 8500.85],
              [14600.30, 16520.35]
            ], // rango de precios por cantidad
          ],
          'pureza'  => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
            'prices_by_quantity' => [
              [850, 1100],
              [7200.35, 8500.85],
              [14600.30, 16520.35]
            ], // rango de precios por cantidad
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
            'prices_by_quantity' => [
              [850, 1100],
              [7200.35, 8500.85],
              [14600.30, 16520.35]
            ], // rango de precios por cantidad
          ],
        ]
      ],
      'azucar' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'azucar')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'ledesma' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'ledesma')->first(),
            'quantity' => [1], // variante en la cantidad
            'prices_by_quantity' => [
              [1600, 1800]
            ], // rango de precios por cantidad
          ],
          'la muñeca' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'la muñeca')->first(),
            'quantity' => [1], // variante en la cantidad
            'prices_by_quantity' => [
              [1000, 1200]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'aceite_girasol'  => [
        'category'   => ProvisionCategory::where('provision_category_name', 'aceite girasol')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity' => [0.9, 1.5, 4.5], // variante en la cantidad
            'prices_by_quantity' => [
              [2200, 2400],
              [3100, 3520.25],
              [6200, 7200.25]
            ], // rango de precios por cantidad
          ],
          'natura'  => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'natura')->first(),
            'quantity' => [0.9, 1.5], // variante en la cantidad
            'prices_by_quantity' => [
              [2200, 2600],
              [3100, 3520.25],
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'levadura_humeda' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'levadura humeda')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'pureza' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity' => [0.05],
            'prices_by_quantity' => [
              [300, 350]
            ], // rango de precios por cantidad
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity' => [0.05],
            'prices_by_quantity' => [
              [320, 370]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'dulce_membrillo' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'dulce de membrillo')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity' => [0.5, 5],
            'prices_by_quantity' => [
              [2300, 2350],
              [9500, 10250.36]
            ], // rango de precios por cantidad
          ],
          'arcor' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'arcor')->first(),
            'quantity' => [0.5, 5],
            'prices_by_quantity' => [
              [2300, 2350],
              [9500, 10250.36]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'dulce_batata' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'dulce de batata')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity' => [0.5, 5],
            'prices_by_quantity' => [
              [2300, 2350],
              [9500, 10250.36]
            ], // rango de precios por cantidad
          ],
          'arcor' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'arcor')->first(),
            'quantity' => [0.5, 5],
            'prices_by_quantity' => [
              [2300, 2350],
              [9500, 10250.36]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'dulce_leche' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'dulce de leche')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'la serenísima' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'la serenísima')->first(),
            'quantity' => [0.25, 1],
            'prices_by_quantity' => [
              [2300, 2350],
              [8500, 9250.36]
            ], // rango de precios por cantidad
          ],
          'milkaut' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'milkaut')->first(),
            'quantity' => [0.25, 1],
            'prices_by_quantity' => [
              [2300, 2350],
              [8500, 9250.36]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'sal_entrefina' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'sal entrefina')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'dos anclas' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'dos anclas')->first(),
            'quantity' => [0.5, 1],
            'prices_by_quantity' => [
              [800, 1050],
              [1500, 1650.36]
            ], // rango de precios por cantidad
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity' => [0.5, 1],
            'prices_by_quantity' => [
              [800, 1050],
              [1500, 1650.36]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'bolsa_plast_10'  => [
        'category'   => ProvisionCategory::where('provision_category_name', 'bolsa plastica numero 10')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'inapack' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'inapack')->first(),
            'quantity' => [1],
            'prices_by_quantity' => [
              [34.25, 50]
            ], // rango de precios por cantidad
          ],
          'empacar' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'empacar')->first(),
            'quantity' => [1],
            'prices_by_quantity' => [
              [34.25, 50]
            ], // rango de precios por cantidad
          ],
        ],
      ],
      'bolsa_papel_10'  => [
        'category'   => ProvisionCategory::where('provision_category_name', 'bolsa de papel numero 10')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'inapack' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'inapack')->first(),
            'quantity' => [1],
            'prices_by_quantity' => [
              [44.25, 54.10]
            ], // rango de precios por cantidad
          ],
          'empacar' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'empacar')->first(),
            'quantity' => [1],
            'prices_by_quantity' => [
              [44.25, 54.10]
            ], // rango de precios por cantidad
          ],
        ],
      ],
    ];

    // Asignar suministros ingrediente a proveedores de ingredientes
    foreach ($suministros_ingrediente as $suministro) {
      // Encontrar la categoría del suministro en el array
      $categoria_key = array_key_first(array_filter($provision_categories, function ($data) use ($suministro) {
        return $data['category']->provision_category_name === $suministro->provision_category->provision_category_name;
      }));

      if (!$categoria_key) continue;

      // Encontrar la marca y sus precios
      $marca = array_filter($provision_categories[$categoria_key]['trademarks'], function ($trademark) use ($suministro) {
        return $trademark['name']->id === $suministro->provision_trademark_id;
      });

      if (empty($marca)) continue;

      $marca = array_values($marca)[0];

      // Encontrar el índice del precio según la cantidad
      $quantity_index = array_search($suministro->provision_quantity, $marca['quantity']);

      if ($quantity_index === false) continue;

      // Obtener rango de precios para esa cantidad
      $price_range = $marca['prices_by_quantity'][$quantity_index];

      // Asignar a proveedores de ingredientes
      foreach ($proveedores_ingredientes as $proveedor) {
        if ($proveedor->provisions()->where('provision_id', $suministro->id)->doesntExist()) {
          // Calcular precio aleatorio dentro del rango para cada proveedor
          $price = rand($price_range[0] * 100, $price_range[1] * 100) / 100;

          $proveedor->provisions()->attach($suministro->id, [
            'price' => $price,
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }
      }
    }

    // Asignar suministros insumo a proveedores de insumos
    foreach ($suministros_insumo as $suministro) {
      // ...similar lógica para encontrar categoría y marca...

      if ($quantity_index === false) continue;

      $price_range = $marca['prices_by_quantity'][$quantity_index];

      // Asignar a proveedores de insumos
      foreach ($proveedores_insumos as $proveedor) {
        if ($proveedor->provisions()->where('provision_id', $suministro->id)->doesntExist()) {
          // Calcular precio aleatorio dentro del rango para cada proveedor
          $price = rand($price_range[0] * 100, $price_range[1] * 100) / 100;

          $proveedor->provisions()->attach($suministro->id, [
            'price' => $price,
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }
      }
    }
  }
}
