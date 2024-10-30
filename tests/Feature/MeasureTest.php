<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MeasureTest extends TestCase
{
  use RefreshDatabase;

  public $measure_data = [
    'measure_name'              => 'gramos',
    'measure_abrv'              => 'gr',
    'measure_base'              => 1000,
    'measure_short_description' => 'unidad de medida en gramos',
    /* 'measure_is_editable'       => true, */
  ];

  /**
   * * crear unidad de medida
   */
  public function test_crear_unidad_de_medida()
  {
    DB::table('measures')->insert($this->measure_data);

    $this->assertDatabaseHas('measures', $this->measure_data);
  }

  /**
   * * existe el modelo de unidad de medida
   */
  public function test_existe_modelo_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $this->assertInstanceOf(\App\Models\Measure::class, $measure);
  }

  /**
   * * editar unidad de medida
   */
  public function test_editar_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $measure->measure_abrv = 'grms';
    $measure->save();

    $this->assertDatabaseHas('measures', ['measure_abrv' => 'grms']);
  }

  /**
   * * eliminar unidad de medida
   */
  public function test_eliminar_unidad_de_medida()
  {
    $measure = \App\Models\Measure::create($this->measure_data);

    $measure->delete();

    $this->assertDatabaseMissing('measures', $measure->toArray());
  }
}
