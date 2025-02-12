<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class RecipeTest extends TestCase
{
  use RefreshDatabase;

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_price' => 4500,
    'product_short_description' => 'lorem ipsup description piola',
  ];

  public $recipe_data = [
    'recipe_title'            => 'pan',
    'recipe_yields'           => 10,
    'recipe_portions'         => 8,
    'recipe_preparation_time' => '00:25:00', //HH:mm:ss
    'recipe_instructions'     => 'lorem ipsum',
    'product_id'              => '',
  ];

  /**
   * crear receta
   * @return void
  */
  public function test_crear_receta()
  {
    $product = Product::create($this->product_data);
    $this->recipe_data['product_id'] = $product->id;
    DB::table('recipes')->insert($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
  }

  /**
   * existe el modelo de receta
   * @return void
  */
  public function test_existe_modelo_receta()
  {
    $product = Product::create($this->product_data);
    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertInstanceOf(\App\Models\Recipe::class, $recipe);
  }

  /**
   * una receta pertenece a un producto
   * @return void
  */
  public function test_una_receta_es_de_un_producto()
  {
    $product = Product::create($this->product_data);
    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
    $this->assertInstanceOf(BelongsTo::class, $recipe->product());
  }

}
