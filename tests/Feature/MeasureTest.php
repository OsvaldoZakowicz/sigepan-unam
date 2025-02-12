<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MeasureTest extends TestCase
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

  /**
   * test crear unidad de medida
   * @return void
   */
  public function test_crear_unidad_de_medida()
  {
    DB::table('measures')->insert($this->measure_data);

    $this->assertDatabaseHas('measures', $this->measure_data);
  }

  /**
   * test existe el modelo de unidad de medida
   * @return void
   */
  public function test_existe_modelo_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(\App\Models\Measure::class, $measure);
  }

  /**
   * test eliminar unidad de medida
   * @return void
   */
  public function test_eliminar_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $measure->delete();

    $this->assertDatabaseMissing('measures', $measure->toArray());
  }

  /**
   * test una unidad de medida se usa en muchos suministros
   * @return void
   */
  public function test_una_unidad_de_medida_se_usa_es_muchos_suministros()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(HasMany::class, $measure->provisions());
  }

  /**
   * test una unidad de medida se asocia a multiples categorias de suministro
   * @return void
   */
  public function test_una_unidad_de_medida_se_asocia_a_categorias_de_suministro(): void
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(HasMany::class, $measure->provision_categories());
  }
}
