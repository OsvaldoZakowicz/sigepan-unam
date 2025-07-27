<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProvisionCategory;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // productos a los que agregar receta
    $products = [
      'product1' => Product::where('product_name', 'pan clasico')->first(),
      'product2' => Product::where('product_name', 'pan integral cinco semillas')->first(),
      'product3' => Product::where('product_name', 'pan dulce con membrillo')->first(),
      'product4' => Product::where('product_name', 'pan dulce con dulce de batata')->first(),
      'product5' => Product::where('product_name', 'bollos de membrillo')->first(),
      'product6' => Product::where('product_name', 'bollos de dulce de batata')->first(),
    ];

    // categorias de suministros para las recetas (ingredientes e insumos)
    $harina_0000    = ProvisionCategory::where('provision_category_name', 'harina 0000')->first();
    $harina_000     = ProvisionCategory::where('provision_category_name', 'harina 000')->first();
    $azucar         = ProvisionCategory::where('provision_category_name', 'azucar')->first();
    $levadura       = ProvisionCategory::where('provision_category_name', 'levadura humeda')->first();
    $aceite_girasol = ProvisionCategory::where('provision_category_name', 'aceite girasol')->first();
    $membrillo      = ProvisionCategory::where('provision_category_name', 'dulce de membrillo')->first();
    $batata         = ProvisionCategory::where('provision_category_name', 'dulce de batata')->first();
    $sal            = ProvisionCategory::where('provision_category_name', 'sal entrefina')->first();
    $bolsa_papel_10 = ProvisionCategory::where('provision_category_name', 'bolsa de papel numero 10')->first();
    $bolsa_plast_10 = ProvisionCategory::where('provision_category_name', 'bolsa plastica numero 10')->first();

    // instruccion de preparacion
    $recipe_instructions = 'Precalentar el horno a 180°C. Mezclar ingredientes secos en un bowl. En otro recipiente, batir huevos con líquidos y aceite. Incorporar mezcla húmeda a la seca sin sobrebatir. Verter en molde engrasado. Hornear 25-30 minutos hasta dorar. Verificar cocción con palillo. Enfriar antes de desmoldar.';

    // recetas por producto
    $recetas = [
      'receta1' => [
        'recipe_title'            => 'receta de ' . $products['product1']->product_name . ' por 12',
        'recipe_yields'           => 12,
        'recipe_portions'         => 3,
        'recipe_preparation_time' => '02:30:00',
        'recipe_instructions'     => $recipe_instructions,
        'product_id'              => $products['product1']->id,
        'categorias' => [
          'categoria1' => ['id' => $harina_0000->id, 'cantidad'  => 3], // kg
          'categoria2' => ['id' => $levadura->id, 'cantidad'  => 0.3], // g
          'categoria3' => ['id' => $aceite_girasol->id, 'cantidad'  => 0.5], // ml
          'categoria4' => ['id' => $sal->id, 'cantidad'  => 0.3], // g
          'categoria5' => ['id' => $bolsa_plast_10->id, 'cantidad'  => 12], // u
        ],
      ],
      'receta2' => [
        'recipe_title'            => 'receta de ' . $products['product2']->product_name . ' por 12',
        'recipe_yields'           => 12,
        'recipe_portions'         => 3,
        'recipe_preparation_time' => '02:30:00',
        'recipe_instructions'     => $recipe_instructions,
        'product_id'              => $products['product2']->id,
        'categorias' => [
          'categoria1' => ['id' => $harina_000->id, 'cantidad'  => 3], // kg
          'categoria2' => ['id' => $levadura->id, 'cantidad'  => 0.3], // g
          'categoria3' => ['id' => $aceite_girasol->id, 'cantidad'  => 0.5], // ml
          'categoria4' => ['id' => $sal->id, 'cantidad'  => 0.3], // g
          'categoria5' => ['id' => $bolsa_plast_10->id, 'cantidad'  => 12], // u
        ],
      ],
      'receta3' => [
        'recipe_title'            => 'receta de ' . $products['product3']->product_name . ' por 12',
        'recipe_yields'           => 12,
        'recipe_portions'         => 3,
        'recipe_preparation_time' => '02:30:00',
        'recipe_instructions'     => $recipe_instructions,
        'product_id'              => $products['product3']->id,
        'categorias' => [
          'categoria1' => ['id' => $harina_0000->id, 'cantidad'  => 3], // kg
          'categoria2' => ['id' => $levadura->id, 'cantidad'  => 0.3], // g
          'categoria3' => ['id' => $aceite_girasol->id, 'cantidad'  => 0.5], // ml
          'categoria4' => ['id' => $azucar->id, 'cantidad'  => 0.3], // g
          'categoria4' => ['id' => $membrillo->id, 'cantidad'  => 0.5], // g
          'categoria5' => ['id' => $bolsa_plast_10->id, 'cantidad'  => 12], // u
        ],
      ],
      'receta4' => [
        'recipe_title'            => 'receta de ' . $products['product4']->product_name . ' por 12',
        'recipe_yields'           => 12,
        'recipe_portions'         => 3,
        'recipe_preparation_time' => '02:30:00',
        'recipe_instructions'     => $recipe_instructions,
        'product_id'              => $products['product4']->id,
        'categorias' => [
          'categoria1' => ['id' => $harina_0000->id, 'cantidad'  => 3], // kg
          'categoria2' => ['id' => $levadura->id, 'cantidad'  => 0.3], // g
          'categoria3' => ['id' => $aceite_girasol->id, 'cantidad'  => 0.5], // ml
          'categoria4' => ['id' => $azucar->id, 'cantidad'  => 0.3], // g
          'categoria4' => ['id' => $batata->id, 'cantidad'  => 0.5], // g
          'categoria5' => ['id' => $bolsa_plast_10->id, 'cantidad'  => 12], // u
        ],
      ],
      'receta5' => [
        'recipe_title'            => 'receta de ' . $products['product5']->product_name . ' por 48',
        'recipe_yields'           => 48,
        'recipe_portions'         => 1,
        'recipe_preparation_time' => '04:30:00',
        'recipe_instructions'     => $recipe_instructions,
        'product_id'              => $products['product5']->id,
        'categorias' => [
          'categoria1' => ['id' => $harina_0000->id, 'cantidad'  => 6], // kg
          'categoria2' => ['id' => $levadura->id, 'cantidad'  => 0.6], // g
          'categoria3' => ['id' => $aceite_girasol->id, 'cantidad'  => 0.5], // ml
          'categoria4' => ['id' => $azucar->id, 'cantidad'  => 0.9], // g
          'categoria4' => ['id' => $membrillo->id, 'cantidad'  => 0.9], // g
        ],
      ],
      'receta6' => [
        'recipe_title'            => 'receta de ' . $products['product6']->product_name . ' por 48',
        'recipe_yields'           => 48,
        'recipe_portions'         => 1,
        'recipe_preparation_time' => '04:30:00',
        'recipe_instructions'     => $recipe_instructions,
        'product_id'              => $products['product6']->id,
        'categorias' => [
          'categoria1' => ['id' => $harina_0000->id, 'cantidad'  => 6], // kg
          'categoria2' => ['id' => $levadura->id, 'cantidad'  => 0.6], // g
          'categoria3' => ['id' => $aceite_girasol->id, 'cantidad'  => 0.5], // ml
          'categoria4' => ['id' => $azucar->id, 'cantidad'  => 0.9], // g
          'categoria4' => ['id' => $batata->id, 'cantidad'  => 0.9], // g
        ],
      ],
    ];

    // crear recetas con cantidades de elaboracion
    foreach ($recetas as $receta) {

      $new_receta = Recipe::create([
        'recipe_title'            => $receta['recipe_title'],
        'recipe_yields'           => $receta['recipe_yields'],
        'recipe_portions'         => $receta['recipe_portions'],
        'recipe_preparation_time' => $receta['recipe_preparation_time'],
        'recipe_instructions'     => $receta['recipe_instructions'],
        'product_id'              => $receta['product_id'],
      ]);

      foreach ($receta['categorias'] as $categoria) {
        $new_receta->provision_categories()->attach($categoria['id'], ['quantity' => $categoria['cantidad']]);
      }

    }
  }
}
