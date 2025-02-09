<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

  public $user_data = [
    'name' => 'user',
    'email' => 'user@mail.com',
    'password' => '12345678',
  ];

  public  $address_data = [
    'street' => 'calle1',
    'number' => '123',
    'postal_code' => '3350',
    'city' => 'apotoles',
  ];

  public  $supplier_data = [
    'company_name' => 'arcor',
    'company_cuit' => '12345678912',
    'iva_condition' => 'monotributista',
    'phone_number' => '3755121447',
    'short_description' => 'description',
    'status_is_active' => true,
    'status_description' => 'dscription',
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
   * crear proveedor
   * @return Supplier
  */
  public function crearProveedor()
  {
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];

    return Supplier::create($this->supplier_data);
  }

  /**
   * crear suministro
   * @return void
  */
  public function test_crear_suministro()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $this->assertDatabaseHas('provisions', $this->provision_data);
    $this->assertInstanceOf(Provision::class, $provision);
  }

  /**
   * eliminar suministro
   * @return void
  */
  public function test_eliminar_suministro()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $provision->delete();

    $this->assertDatabaseMissing('provisions', $provision->toArray());
  }

  /**
   * un suministro tiene una marca
   * @return void
  */
  public function test_un_suministro_tiene_una_marca()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $provision = Provision::create($this->provision_data);

    $this->assertInstanceOf(BelongsTo::class, $provision->trademark());
  }

  /**
   * un suministro es de un tipo especifico
   * @return void
  */
  public function test_un_suministro_es_de_un_tipo()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $this->assertInstanceOf(BelongsTo::class, $provision->type());
  }

  /**
   * un suministro tiene una unidad de medida
   * @return void
  */
  public function test_un_suministro_tiene_una_unidad_de_medida()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $this->assertInstanceOf(BelongsTo::class, $provision->measure());
  }

  /**
   * un suministro pertenece muchas recetas
   * @return void
  */
  public function test_un_suministro_pertenece_a_recetas()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);

    $this->assertInstanceOf(BelongsToMany::class, $provision->recipes());
  }

  /**
   * asignar suministro a un proveedor
   * @return void
  */
  public function test_asignar_suministro_a_proveedor()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure);
    $supplier       = $this->crearProveedor();

    $supplier->provisions()->attach($provision->id, ['price' => 100]);

    $this->assertDatabaseHas('provision_supplier', [
      'provision_id' => $provision->id,
      'supplier_id' => $supplier->id,
      'price' => 100
    ]);

    $this->assertInstanceOf(BelongsToMany::class, $provision->suppliers());
    $this->assertInstanceOf(BelongsToMany::class, $supplier->provisions());
  }
}
