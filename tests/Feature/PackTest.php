<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\ProvisionTrademark;
use App\Models\Measure;
use App\Models\ProvisionType;
use App\Models\Provision;
use App\Models\ProvisionCategory;
use App\Models\Pack;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    'unit_name' => 'litro',
    'base_value' => 1,
    'unit_symbol' => 'L',
    'conversion_unit' => 'mililitros',
    'conversion_factor' => 1000,
    'conversion_symbol' => 'mL',
    'short_description' => 'unidad de medida en litros'
  ];

  // suministro
  public $provision_data = [
    'provision_name' => 'aceite',
    'provision_quantity' => 900,
    'provision_short_description' => 'descripcion',
  ];

  public $provision_category_data = [
    'provision_category_name'        => 'aceite mezcla',
    'provision_category_is_editable' => false,
    'measure_id'                     => '',
    'provision_type_id'              => ''
  ];

  // pack
  public $pack_data = [
    'pack_name' => 'pack de aceite',
    'pack_units' => 6,
    'pack_quantity' => 5.40,
  ];

  // usuario proveedor
  public $user_data = [
    'name' => 'user',
    'email' => 'user@mail.com',
    'password' => '12345678',
  ];

  // direccion proveedor
  public  $address_data = [
    'street' => 'calle1',
    'number' => '123',
    'postal_code' => '3350',
    'city' => 'apotoles',
  ];

  // proveedor
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
   * crear categoria con medida y tipo
   * @param Measure $measure
   * @param ProvisionType $type
   * @return ProvisionCategory
   */
  public function crearCategoriaDeSuministro($measure, $type): ProvisionCategory
  {
    $this->provision_category_data['measure_id'] = $measure->id;
    $this->provision_category_data['provision_type_id'] = $type->id;

    return ProvisionCategory::create($this->provision_category_data);
  }

  /**
   * crear un suministro
   * @param ProvisionTrademark $Trademark
   * @param ProvisionType $Type
   * @param Measure $Measure
   * @param ProvisionCategory $category
   * @return Provision
  */
  public function crearSuministro($Trademark, $Type, $Measure, $category)
  {
    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $Trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $Type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $Measure->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_category_id', $category->id);

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
   * test crear un pack de suministros
   * @return void
  */
  public function test_crear_pack()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $category       = $this->crearCategoriaDeSuministro($measure, $provision_type);
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure, $category);

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
    $category       = $this->crearCategoriaDeSuministro($measure, $provision_type);
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure, $category);

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
    $category       = $this->crearCategoriaDeSuministro($measure, $provision_type);
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure, $category);

    $pack           = $this->crearPack($provision);

    $this->assertInstanceOf(HasMany::class, $provision->packs());
  }

  /**
   * test asignar pack a un proveedor
  */
  public function test_asignar_pack_a_proveedor()
  {
    $trademark      = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure        = $this->crearMedida();
    $category       = $this->crearCategoriaDeSuministro($measure, $provision_type);
    $provision      = $this->crearSuministro($trademark, $provision_type, $measure, $category);
    $pack           = $this->crearPack($provision);
    $supplier       = $this->crearProveedor();

    $supplier->packs()->attach($pack->id, ['price' => 100]);

    $this->assertDatabaseHas('pack_supplier', [
      'pack_id' => $pack->id,
      'supplier_id' => $supplier->id,
      'price' => 100
    ]);

    // relaciones entre pack y proveedor
    $this->assertInstanceOf(BelongsToMany::class, $pack->suppliers());
    $this->assertInstanceOf(BelongsToMany::class, $supplier->packs());
  }

}
