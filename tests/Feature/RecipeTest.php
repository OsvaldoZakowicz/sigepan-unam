<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class RecipeTest extends TestCase
{
  use RefreshDatabase;

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_short_description' => 'lorem ipsup description piola',
    'product_expires_in' => 2,
    'product_in_store' => true,
    'product_image_path' => 'productos/pan.jpg'
  ];

  public $recipe_data = [
    'recipe_title' => 'receta de algo',
    'recipe_yields' => 5,
    'recipe_portions' => 3,
    'recipe_preparation_time' => '01:25:00',
    'recipe_instructions' => 'lorem ipsum recetitus',
    'product_id' => null
  ];

  public $category_data = [
    'provision_category_name' => 'harina 0000',
    'provision_category_is_editable' => false,
    'measure_id' => null,
    'provision_type_id' => null
  ];

  public $measure_data = [
    'unit_name' => 'kilogramo',
    'base_value' => 1,
    'unit_symbol' => 'Kg',
    'conversion_unit' => 'gramos',
    'conversion_factor' => 1000,
    'conversion_symbol' => 'g',
    'short_description' => 'unidad de medida en kilogramos o gramos',
  ];

  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'descripcion',
  ];

  /**
   * @testCase TC001.
   * @purpose Crear una receta.
   * @expectedResult Se crea una receta en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_crear_receta()
  {
    $product = Product::create($this->product_data);

    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
    $this->assertInstanceOf(\App\Models\Recipe::class, $recipe);
  }

  /**
   * @testCase TC002.
   * @purpose Una receta es de un producto.
   * @expectedResult Se verifica que una receta es de un producto.
   * @observations Ninguna.
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

  /**
   * @testCase TC003.
   * @purpose Una receta tiene ingredientes (categorias de suministros) asociados.
   * @expectedResult Se verifica que una receta tiene ingredientes con cantidades.
   * @observations Necesita una categoria, unidad de medida y tipo de suministro.
   * @return void
   */
  public function test_receta_tiene_ingredientes()
  {
    $product = Product::create($this->product_data);

    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    // ingrediente
    $type = \App\Models\ProvisionType::create($this->provision_type_data);
    // kilogramos
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->category_data['measure_id'] = $measure->id;
    $this->category_data['provision_type_id'] = $type->id;

    // harina 0000
    $category = \App\Models\ProvisionCategory::create($this->category_data);

    $recipe->provision_categories()->attach($category->id, [
      'quantity' => 4, // 4 kilogramos
    ]);

    $this->assertDatabaseHas('category_recipe', [
      'recipe_id' => $recipe->id,
      'category_id' => $category->id,
      'quantity' => 4,
    ]);
    $this->assertInstanceOf(BelongsToMany::class, $recipe->provision_categories());
  }

}
