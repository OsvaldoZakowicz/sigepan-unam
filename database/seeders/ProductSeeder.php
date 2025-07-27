<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tag;
use App\Models\Price;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // etiquetas a usar
    $dulce    = Tag::where('tag_name', 'dulce')->first();
    $salado   = Tag::where('tag_name', 'salado')->first();
    $blanco   = Tag::where('tag_name', 'blanco')->first();
    $integral = Tag::where('tag_name', 'integral')->first();


    // productos a crear
    $products = [
      'product1' => [
        'product_name'              => 'pan clasico',
        'product_short_description' => 'pan clasico ideal para acompañar las comidas',
        'product_expires_in'        => 4,
        'product_in_store'          => true,
        'product_image_path'        => 'productos/pan.jpg',
        'prices' => [
          'price1' => [
            'quantity'    => 1,
            'price'       => 4500,
            'description' => 'unidad',
            'is_default'  => false,
          ],
          'price2' => [
            'quantity'    => 2,
            'price'       => 8000,
            'description' => 'promo por dos',
            'is_default'  => true,
          ],
        ],
        'tags' => [
          $salado,
          $blanco
        ]
      ],
      'product2' => [
        'product_name'              => 'pan integral cinco semillas',
        'product_short_description' => 'pan hecho con harina integral y semillas',
        'product_expires_in'        => 4,
        'product_in_store'          => true,
        'product_image_path'        => 'productos/pan-salvado.jpg',
        'prices' => [
          'price1' => [
            'quantity'    => 1,
            'price'       => 5200,
            'description' => 'unidad',
            'is_default'  => false,
          ],
          'price2' => [
            'quantity'    => 2,
            'price'       => 9000,
            'description' => 'promo por dos',
            'is_default'  => true,
          ],
        ],
        'tags' => [
          $salado,
          $integral
        ]
      ],
      'product3' => [
        'product_name'              => 'pan dulce con membrillo',
        'product_short_description' => 'pan dulce relleno con abundante membrillo y cubierto de almibar',
        'product_expires_in'        => 4,
        'product_in_store'          => true,
        'product_image_path'        => 'productos/pan-membrillo.jpg',
        'prices' => [
          'price1' => [
            'quantity'    => 1,
            'price'       => 5000,
            'description' => 'unidad',
            'is_default'  => true,
          ],
        ],
        'tags' => [
          $dulce,
          $blanco
        ]
      ],
      'product4' => [
        'product_name'              => 'pan dulce con dulce de batata',
        'product_short_description' => 'pan dulce relleno con abundante dulce de batata y cubierto de almibar',
        'product_expires_in'        => 4,
        'product_in_store'          => true,
        'product_image_path'        => 'productos/pan-batata.jpg',
        'prices' => [
          'price1' => [
            'quantity'    => 1,
            'price'       => 5000,
            'description' => 'unidad',
            'is_default'  => false,
          ],
          'price2' => [
            'quantity'    => 2,
            'price'       => 9000,
            'description' => 'promo por dos',
            'is_default'  => true,
          ],
        ],
        'tags' => [
          $dulce,
          $blanco
        ]
      ],
      'product5' => [
        'product_name'              => 'bollos de membrillo',
        'product_short_description' => 'bollos rellenos con membrillo y cubiertos de almibar',
        'product_expires_in'        => 4,
        'product_in_store'          => true,
        'product_image_path'        => 'productos/bollo-membrillo.jpg',
        'prices' => [
          'price1' => [
            'quantity'    => 1,
            'price'       => 600,
            'description' => 'unidad',
            'is_default'  => false,
          ],
          'price2' => [
            'quantity'    => 6,
            'price'       => 3000,
            'description' => 'media docena',
            'is_default'  => false,
          ],
          'price3' => [
            'quantity'    => 12,
            'price'       => 5500,
            'description' => 'una docena',
            'is_default'  => true,
          ],
        ],
        'tags' => [
          $dulce,
          $blanco
        ]
      ],
      'product6' => [
        'product_name'              => 'bollos de dulce de batata',
        'product_short_description' => 'bollos rellenos con dulce de batata y cubiertos de almibar',
        'product_expires_in'        => 4,
        'product_in_store'          => true,
        'product_image_path'        => 'productos/bollo-batata.jpg',
        'prices' => [
          'price1' => [
            'quantity'    => 1,
            'price'       => 600,
            'description' => 'unidad',
            'is_default'  => false,
          ],
          'price2' => [
            'quantity'    => 6,
            'price'       => 3000,
            'description' => 'media docena',
            'is_default'  => false,
          ],
          'price3' => [
            'quantity'    => 12,
            'price'       => 5500,
            'description' => 'una docena',
            'is_default'  => true,
          ],
        ],
        'tags' => [
          $dulce,
          $blanco
        ]
      ],
    ];

    // crear productos con precios
    // las etiquetas ya existen en la BD
    foreach ($products as $key => $product_scheme) {

      // crear el producto
      $new_product = Product::create([
        'product_name'              => $product_scheme['product_name'],
        'product_short_description' => $product_scheme['product_short_description'],
        'product_expires_in'        => $product_scheme['product_expires_in'],
        'product_in_store'          => $product_scheme['product_in_store'],
        'product_image_path'        => $product_scheme['product_image_path'],
      ]);

      // asociar las etiquetas
      foreach ($product_scheme['tags'] as $tag) {
        $new_product->tags()->attach($tag->id);
      }

      // agregar los precios
      foreach ($product_scheme['prices'] as $price) {
        $new_product->addPrice(
          $price['quantity'],
          $price['price'],
          $price['description'],
          $price['is_default']
        );
      }

    }
  }
}
