<?php

namespace Tests\Feature;

use App\Models\Measure;
use App\Models\ProvisionCategory;
use App\Models\ProvisionType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProvisionCategoryTest extends TestCase
{
  use RefreshDatabase;

  public $measure_data = [
    'unit_name' => 'litro',
    'base_value' => 1,
    'unit_symbol' => 'L',
    'conversion_unit' => 'mililitros',
    'conversion_factor' => 1000,
    'conversion_symbol' => 'mL',
    'short_description' => 'unidad de medida en litros'
  ];

  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'un ingrediente es un componente esencial de la preparacion de la receta de un producto',
  ];

  public $provision_category_data = [
    'provision_category_name'        => 'aceite mezcla',
    'provision_category_is_editable' => false,
    'measure_id'                     => '',
    'provision_type_id'              => ''
  ];

  /**
   * crear categoria con medida y tipo
   * @return ProvisionCategory
   */
  public function crearCategoriaDeSuministro(): ProvisionCategory
  {
    $measure = Measure::create($this->measure_data);
    $type    = ProvisionType::create($this->provision_type_data);

    $this->provision_category_data['measure_id'] = $measure->id;
    $this->provision_category_data['provision_type_id'] = $type->id;

    return ProvisionCategory::create($this->provision_category_data);
  }

  /**
   * test crear categoria
   * @return void
   */
  public function test_crear_categoria(): void
  {
    $category = $this->crearCategoriaDeSuministro();

    $this->assertDatabaseHas('provision_categories', $this->provision_category_data);
    $this->assertInstanceOf(ProvisionCategory::class, $category);
  }

  /**
   * test una categoria tiene una unidad de medida
   * @return void
   */
  public function test_una_categoria_tiene_unidad_de_medida(): void
  {
    $category = $this->crearCategoriaDeSuministro();

    $this->assertInstanceOf(BelongsTo::class, $category->measure());
  }

  /**
   * test una categoria tiene un tipo de suministro
   * @return void
   */
  public function test_una_categoria_tiene_tipo_de_suministro(): void
  {
    $category = $this->crearCategoriaDeSuministro();

    $this->assertInstanceOf(BelongsTo::class, $category->provision_type());
  }

  /**
   * test una categoria tiene muchos suministros asociados
   * @return void
   */
  public function test_una_categoria_tiene_suministros(): void
  {
    $category = $this->crearCategoriaDeSuministro();

    $this->assertInstanceOf(HasMany::class, $category->provisions());
  }
}
