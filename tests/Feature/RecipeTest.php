<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
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

  public $trademark_data = [
    'provision_trademark_name' => 'blancaflor'
  ];

  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'un ingrediente',
  ];

  public $measure_data = [
    'measure_name'              => 'kilogramos',
    'measure_abrv'              => 'kg',
    'measure_base'              => 1000,
    'measure_base_abrv'         => 'g',
    'measure_short_description' => 'unidad de medida en kilogramos'
  ];

  public $provision_data = [
    'provision_name' => 'aceite',
    'provision_quantity' => 900,
    'provision_short_description' => 'descripcion',
  ];

  /**
   * crear una marca
   * @return ProvisionTrademark
  */
  public function crearMarca()
  {
    return ProvisionTrademark::create($this->trademark_data);
  }

  /**
   * crear tipo de suministro
   * @return ProvisionType
  */
  public function crearTipo()
  {
    return ProvisionType::create($this->provision_type_data);
  }

  /**
   * crear una unidad de medida
   * @return Measure
  */
  public function crearMedida()
  {
    return Measure::create($this->measure_data);
  }

  /**
   * crear un suministro
   * @param ProvisionTrademark $Trademark
   * @param ProvisionType $Type
   * @param Measure $Measure
   * @return Provision
  */
  public function crearSuministro($Trademark, $Type, $Measure)
  {
    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $Trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $Type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $Measure->id);

    return Provision::create($this->provision_data);
  }

  /**
   * crear producto
   * @return Product
  */
  public function crearProducto(): Product
  {
    return Product::create($this->product_data);
  }

  /**
   * crear receta
   * @return void
  */
  public function test_crear_receta()
  {
    $product = $this->crearProducto();
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
    $product = $this->crearProducto();
    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertInstanceOf(\App\Models\Recipe::class, $recipe);
  }

  /**
   * una receta tiene muchos suministros asociados
   * @return void
  */
  public function test_una_receta_tiene_muchos_suministros()
  {
    $product = $this->crearProducto();
    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertInstanceOf(BelongsToMany::class, $recipe->provisions());
  }

  /**
   * asignar un suministro a la receta
   * @return void
  */
  public function test_asignar_suministro_a_una_receta()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $product        = $this->crearProducto();
    $this->recipe_data['product_id'] = $product->id;

    $recipe         = \App\Models\Recipe::create($this->recipe_data);

    $recipe->provisions()->attach($provision->id, ['recipe_quantity' => 1.5]);

    $this->assertDatabaseHas('provision_recipe', [
      'provision_id'    => $provision->id,
      'recipe_id'       => $recipe->id,
      'recipe_quantity' => 1.5
    ]);

    $this->assertInstanceOf(BelongsToMany::class, $provision->recipes());
  }

  /**
   * una receta pertenece a un producto
   * @return void
  */
  public function test_una_receta_es_de_un_producto()
  {
    $product = $this->crearProducto();
    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
    $this->assertInstanceOf(BelongsTo::class, $recipe->product());
  }

}
