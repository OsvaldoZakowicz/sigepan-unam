<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use App\Models\PeriodStatus;
use App\Models\PreOrderPeriod;
use App\Models\PreOrder;
use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\ProvisionCategory;
use App\Models\Pack;
use Tests\TestCase;

class PreOrderTest extends TestCase
{
  use RefreshDatabase;

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

  public $period_status_data = [
    'status_name' => 'programado',
    'status_code' => 0,
    'status_short_description' => 'lorem ipsum'
  ];

  // sera un periodo de ordenes de compra sin relacion a un periodo presupuestario
  public $pre_order_period_data = [
    'quotation_period_id' => null,
    'period_code' => 'TEST-001',
    'period_start_at' => '2024-01-01',
    'period_end_at' => '2024-12-31',
    'period_short_description' => 'Test Period',
    'period_status_id' => null
  ];

  public $pre_order_data = [
    'pre_order_period_id' => null,
    'supplier_id' => null,
    'pre_order_code' => 'TEST-001',
    'quotation_reference' => 'TEST-001',
    'status' => 'pendiente',
    'is_approved_by_supplier' => false,
    'is_approved_by_buyer' => false,
  ];

  //* pack y suministro

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
   * test crear pre orden de compra
   * @return void
   */
  public function test_crear_pre_orden_de_compra(): void
  {
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];
    $supplier = Supplier::create($this->supplier_data);

    $period_status = PeriodStatus::create($this->period_status_data);
    $this->pre_order_period_data['period_status_id'] = $period_status->id;
    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->pre_order_data['pre_order_period_id'] = $pre_order_period->id;
    $this->pre_order_data['supplier_id'] = $supplier->id;
    $pre_order = PreOrder::create($this->pre_order_data);

    $this->assertInstanceOf(PreOrder::class, $pre_order);
    $this->assertDatabaseHas('pre_orders', $this->pre_order_data);
  }

  /**
   * test una pre orden pertenece a un proveedor
   * @return void
   */
  public function test_una_pre_orden_pertenece_a_un_proveedor(): void
  {
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];
    $supplier = Supplier::create($this->supplier_data);

    $period_status = PeriodStatus::create($this->period_status_data);
    $this->pre_order_period_data['period_status_id'] = $period_status->id;
    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->pre_order_data['pre_order_period_id'] = $pre_order_period->id;
    $this->pre_order_data['supplier_id'] = $supplier->id;
    $pre_order = PreOrder::create($this->pre_order_data);

    $this->assertInstanceOf(BelongsTo::class, $pre_order->supplier());
    $this->assertDatabaseHas('suppliers', $this->supplier_data);
  }

  /**
   * test una pre orden tiene packs de suministros
   * @return void
   */
  public function test_una_pre_orden_tiene_packs_de_suministros(): void
  {
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];
    $supplier = Supplier::create($this->supplier_data);

    $period_status = PeriodStatus::create($this->period_status_data);
    $this->pre_order_period_data['period_status_id'] = $period_status->id;
    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->pre_order_data['pre_order_period_id'] = $pre_order_period->id;
    $this->pre_order_data['supplier_id'] = $supplier->id;
    $pre_order = PreOrder::create($this->pre_order_data);

    $trademark = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure = $this->crearMedida();
    $category = $this->crearCategoriaDeSuministro($measure, $provision_type);
    $provision = $this->crearSuministro($trademark, $provision_type, $measure, $category);
    $pack = $this->crearPack($provision);

    $pre_order->packs()->attach($pack->id, [
      'has_stock' => true,
      'quantity' => 5,
      'unit_price' => 10.50,
      'total_price' => 52.50
    ]);

    $this->assertInstanceOf(BelongsToMany::class, $pre_order->packs());
    $this->assertDatabaseHas('pre_order_pack', [
      'pre_order_id' => $pre_order->id,
      'pack_id' => $pack->id,
      'has_stock' => true,
      'quantity' => 5,
      'unit_price' => 10.50,
      'total_price' => 52.50
    ]);
  }

  /**
   * test una pre orden tiene suministros
   * @return void
   */
  public function test_una_pre_orden_tiene_suministros(): void
  {
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];
    $supplier = Supplier::create($this->supplier_data);

    $period_status = PeriodStatus::create($this->period_status_data);
    $this->pre_order_period_data['period_status_id'] = $period_status->id;
    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->pre_order_data['pre_order_period_id'] = $pre_order_period->id;
    $this->pre_order_data['supplier_id'] = $supplier->id;
    $pre_order = PreOrder::create($this->pre_order_data);

    $trademark = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure = $this->crearMedida();
    $category = $this->crearCategoriaDeSuministro($measure, $provision_type);
    $provision = $this->crearSuministro($trademark, $provision_type, $measure, $category);

    $pre_order->provisions()->attach($provision->id, [
      'has_stock' => true,
      'quantity' => 5,
      'unit_price' => 10.50,
      'total_price' => 52.50
    ]);

    $this->assertInstanceOf(BelongsToMany::class, $pre_order->provisions());
    $this->assertDatabaseHas('pre_order_provision', [
      'pre_order_id' => $pre_order->id,
      'provision_id' => $provision->id,
      'has_stock' => true,
      'quantity' => 5,
      'unit_price' => 10.50,
      'total_price' => 52.50
    ]);
  }

}
