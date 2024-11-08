<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
use App\Models\Provision;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ProvisionTest extends TestCase
{
  use RefreshDatabase;

  public $trademark_data = [
    'provision_trademark_name' => 'blancaflor'
  ];

  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'un ingrediente',
  ];

  public $measure_data = [
    'measure_name' => 'gramos',
    'measure_abrv' => 'gr',
    'measure_base' => 1000,
    'measure_short_description' => 'unidad de medida en gramos',
    'measure_is_editable' => false
  ];

  public $provision_data = [
    'provision_name' => 'aceite',
    'provision_quantity' => 900,
    'provision_short_description' => 'descripcion',
  ];

  /**
   * * crear unidad de medida
   */
  public function test_crear_suministro()
  {
    $trademark = ProvisionTrademark::create($this->trademark_data);
    $provision_type = ProvisionType::create($this->provision_type_data);
    $measure = Measure::create($this->measure_data);

    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $provision_type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $measure->id);

    $provision = Provision::create($this->provision_data);

    // existe la provision creada en la base de datos
    $this->assertDatabaseHas('provisions', $this->provision_data);
    // la provision es una instancia del modelo Provision
    $this->assertInstanceOf(Provision::class, $provision);
  }

  /**
   * * eliminar provision
   */
  public function test_eliminar_suministro()
  {
    $trademark = ProvisionTrademark::create($this->trademark_data);
    $provision_type = ProvisionType::create($this->provision_type_data);
    $measure = Measure::create($this->measure_data);

    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $provision_type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $measure->id);

    $provision = Provision::create($this->provision_data);

    $provision->delete();

    // no existe la provision en la base de datos
    $this->assertDatabaseMissing('provisions', $provision->toArray());
  }

  /**
   * * un suministro tiene una marca
   */
  public function test_un_suministro_tiene_una_marca()
  {
    $trademark = ProvisionTrademark::create($this->trademark_data);
    $provision_type = ProvisionType::create($this->provision_type_data);
    $measure = Measure::create($this->measure_data);

    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $provision_type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $measure->id);

    $provision = Provision::create($this->provision_data);

    $this->assertInstanceOf(BelongsTo::class, $provision->trademark());
  }

  /**
   * * un suministro tiene es de un tipo especifico
   */
  public function test_un_suministro_es_de_un_tipo()
  {
    $trademark = ProvisionTrademark::create($this->trademark_data);
    $provision_type = ProvisionType::create($this->provision_type_data);
    $measure = Measure::create($this->measure_data);

    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $provision_type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $measure->id);

    $provision = Provision::create($this->provision_data);

    $this->assertInstanceOf(BelongsTo::class, $provision->type());
  }

  /**
   * * un suministro tiene una unidad de medida
   */
  public function test_un_suministro_tiene_una_unidad_de_medida()
  {
    $trademark = ProvisionTrademark::create($this->trademark_data);
    $provision_type = ProvisionType::create($this->provision_type_data);
    $measure = Measure::create($this->measure_data);

    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $provision_type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $measure->id);

    $provision = Provision::create($this->provision_data);

    $this->assertInstanceOf(BelongsTo::class, $provision->measure());
  }
}
