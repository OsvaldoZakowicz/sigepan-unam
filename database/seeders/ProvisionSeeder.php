<?php

namespace Database\Seeders;

use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
use App\Models\ProvisionCategory;
use App\Models\Provision;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvisionSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {

    /**
     * * categorias a usar:
     * - la categoria determina el tipo de suministro: ProvisionType,
     * - la categoria determina la unidad de medida: Measure,
     * [
     *  'key' => [
     *    'category' => ProvisionCategory,
     *    'trademarks' => [
     *      'key' => ProvisionTrademark,
     *      'key' => ProvisionTrademark
     *    ]
     *  ], ... [],
     * ]
     */
    $provision_categories = [
      'harina_000' => [
        'category'   => ProvisionCategory::where('provision_category_name', 'harina 000')
          ->with(['measure', 'provision_type'])->first(),
        'trademarks' => [
          'marolio' => [
            'name'     => ProvisionTrademark::where('provision_trademark_name', 'marolio')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
          ],
          'pureza' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
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
          ],
          'pureza'  => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'pureza')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity' => [1, 5, 25], // variante en la cantidad
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
          ],
          'la muñeca' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'la muñeca')->first(),
            'quantity' => [1], // variante en la cantidad
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
          ],
          'natura'  => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'natura')->first(),
            'quantity' => [0.9, 1.5], // variante en la cantidad
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
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity' => [0.05],
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
          ],
          'arcor' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'arcor')->first(),
            'quantity' => [0.5, 5],
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
          ],
          'arcor' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'arcor')->first(),
            'quantity' => [0.5, 5],
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
          ],
          'milkaut' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'milkaut')->first(),
            'quantity' => [0.25, 1],
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
          ],
          'calsa' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'calsa')->first(),
            'quantity' => [0.5, 1],
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
          ],
          'empacar' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'empacar')->first(),
            'quantity' => [1],
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
          ],
          'empacar' => [
            'name' => ProvisionTrademark::where('provision_trademark_name', 'empacar')->first(),
            'quantity' => [1],
          ],
        ],
      ],
    ];

    /**
     * * estructura de un suministro:
     * 'provision_name',               // string  = $provision_categories['category']->provision_category_name.
     * 'provision_quantity',           // decimal = volumen del suministro.
     * 'provision_short_description',  // string  = descripcion corta sobre el suministro.
     * 'provision_trademark_id',       // id de la marca = $provision_categories['trademarks']['trademark']->id.
     * 'provision_category_id',        // id de la categoria = $provision_categories['category']->id.
     * 'provision_type_id',            // id del tipo = $provision_categories['category']->provision_type->id
     * 'measure_id',                   // id de la unidad de medida $provision_categories['category']->measure->id.
     *
     * NOTA: Volumen del suministro:
     * - si la unidad de medida (unit_name) es: 'kilogramo' o 'litro', pueden asignarse valores
     * provision_quantity al suministro de por ejemplo 1.5 (K o L), 0.9 (g o mL).
     * - si la unidad de medida (unit_name) es: 'unidad' sera siempre un numero entero 1, 2, 3, n.
     * - si la unidad de medida (unit_name) es: 'metro', pueden asignarse valores
     * provision_quantity al suministro de por ejemplo 1.5 (m) o 0.8 (cm).
     *
     * NOTA: Tipo de suministro, existen dos tipos;
     * - ingrediente o insumo. La relacion es 'provision_type_id' en cada suministro.
     */

    // crear suministros por cada marca y cantidad en cada categoría
    foreach ($provision_categories as $key => $data) {
      // categoria (conoce unidad de medida y tipo)
      $category = $data['category'];

      foreach ($data['trademarks'] as $trademark_key => $trademark) {
        // Por cada cantidad definida para esta marca
        foreach ($trademark['quantity'] as $quantity) {
          Provision::create([
            'provision_name'              => $category->provision_category_name,
            'provision_quantity'          => $quantity,
            'provision_short_description' => "suministro {$category->provision_category_name} marca {$trademark['name']->provision_trademark_name} x {$quantity}{$category->measure->unit_symbol}",
            'provision_trademark_id'      => $trademark['name']->id,
            'provision_category_id'       => $category->id,
            'provision_type_id'           => $category->provision_type->id,
            'measure_id'                  => $category->measure->id,
          ]);
        }
      }
    }
  }
}
