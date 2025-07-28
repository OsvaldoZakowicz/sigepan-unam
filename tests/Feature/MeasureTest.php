<?php

namespace Tests\Feature;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MeasureTest extends TestCase
{
  use RefreshDatabase;
  use WithoutModelEvents;

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
   * @testCase TC001.
   * @purpose Crear unidad de medida.
   * @expectedResult Se crea una unidad de medida en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_crear_unidad_de_medida()
  {
    \App\Models\Measure::create($this->measure_data);

    $this->assertDatabaseHas('measures', $this->measure_data);
  }

  /**
   * @testCase TC002.
   * @purpose Existe modelo unidad de medida.
   * @expectedResult La instancia de Measure creada es en efecto una unidad de medida.
   * @observations Ninguna.
   * @return void
   */
  public function test_existe_modelo_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(\App\Models\Measure::class, $measure);
  }

  /**
   * @testCase TC003.
   * @purpose Eliminar unidad de medida.
   * @expectedResult La unidad de medida es eliminada del sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_eliminar_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $measure->delete();

    $this->assertDatabaseMissing('measures', $measure->toArray());
  }

  /**
   * @testCase TC004.
   * @purpose Una unidad de medida se usa en muchos suministros.
   * @expectedResult La instancia de Measure creada puede relacionarse a uno o mas suministros.
   * @observations Ninguna.
   * @return void
   */
  public function test_una_unidad_de_medida_se_usa_es_muchos_suministros()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(HasMany::class, $measure->provisions());
  }

  /**
   * @testCase TC005.
   * @purpose Una unidad de medida se usa en muchas categorias de suministros.
   * @expectedResult La instancia de Measure creada puede relacionarse a uno o mas categorias de suministros.
   * @observations Ninguna.
   * @return void
   */
  public function test_una_unidad_de_medida_se_asocia_a_categorias_de_suministro(): void
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(HasMany::class, $measure->provision_categories());
  }
}
