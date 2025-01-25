<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\ProvisionTrademark;
use App\Models\Measure;
use App\Models\ProvisionType;
use App\Models\Provision;
use App\Models\Pack;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class PackTest extends TestCase
{

  use RefreshDatabase;

  // marca
  public $trademark_data = [
    'provision_trademark_name' => 'blancaflor'
  ];

  // tipo
  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'un ingrediente',
  ];

  // unidad de medida
  public $measure_data = [
    'measure_name' => 'gramos',
    'measure_abrv' => 'gr',
    'measure_base' => 1000,
    'measure_short_description' => 'unidad de medida en gramos',
    'measure_is_editable' => false
  ];

  // suministro
  public $provision_data = [
    'provision_name' => 'aceite',
    'provision_quantity' => 900,
    'provision_short_description' => 'descripcion',
  ];

  // pack
  public $pack_data = [
    'pack_name' => 'pack de aceite',
    'pack_units' => 6,
    'pack_quantity' => 5.40,
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
   * crear un pack
   * @param Provision $Provision
   * @return Pack
  */
  public function crearPack($provision)
  {
    $this->pack_data = Arr::add($this->pack_data, 'provision_id', $provision->id);

    return Pack::create($this->pack_data);
  }

  /**
   * test crear un pack de suministros
   * @return void
  */
  public function test_crear_pack()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $pack           = $this->crearPack($provision);

    $this->assertDatabaseHas('packs', $this->pack_data);
    $this->assertInstanceOf('App\Models\Pack', $pack);
  }

  /**
   * test un pack pertenece a un suministro
  */
  public function test_pack_es_de_un_suministro()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $pack           = $this->crearPack($provision);

    $this->assertInstanceOf(BelongsTo::class, $pack->provision());
  }

  /**
   * test un suministro tiene muchos packs
  */
  public function test_un_suministro_tiene_muchos_packs()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);
    $pack           = $this->crearPack($provision);

    $this->assertInstanceOf(HasMany::class, $provision->packs());
  }

}
