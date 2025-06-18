<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pack;
use App\Models\User;
use App\Models\Address;
use App\Models\Measure;
use App\Models\Supplier;
use App\Models\Provision;
use App\Models\Quotation;
use Illuminate\Support\Arr;
use App\Models\PeriodStatus;
use App\Models\ProvisionType;
use App\Models\ProvisionCategory;
use App\Models\ProvisionTrademark;
use App\Jobs\UpdateSuppliersPricesJob;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateSuppliersPricesJobTest extends TestCase
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

  public $period_data = [
    'period_code' => 'periodo_1',
    'period_start_at' => '2024-11-26',
    'period_end_at' => '2024-11-28',
    'period_short_description' => 'lorem ipsum',
  ];

  public $trademark_data = [
    'provision_trademark_name' => 'marolio'
  ];

  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'un ingrediente',
  ];

  public $measure_data = [
    'unit_name' => 'litro',
    'base_value' => 1,
    'unit_symbol' => 'L',
    'conversion_unit' => 'mililitros',
    'conversion_factor' => 1000,
    'conversion_symbol' => 'mL',
    'short_description' => 'unidad de medida en litros'
  ];

  public $provision_data = [
    'provision_name' => 'aceite',
    'provision_quantity' => 900,
    'provision_short_description' => 'descripcion',
  ];

  public $category_data = [
    'provision_category_name' => 'aceite',
  ];

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
   * crear una categoria de suministro
   * @param ProvisionType $Type
   * @param Measure $Measure
   * @return ProvisionCategory
   */
  public function crearCategoriaDeSuministro($type, $measure)
  {
    $this->category_data = Arr::add($this->category_data, 'measure_id', $measure->id);
    $this->category_data = Arr::add($this->category_data, 'provision_type_id', $type->id);

    return ProvisionCategory::create($this->category_data);
  }

  /**
   * crear un suministro
   * @param ProvisionTrademark $trademark
   * @param ProvisionType $type
   * @param Measure $measure
   * @param ProvisionCategory $category
   * @return Provision
   */
  public function crearSuministro($trademark, $type, $measure, $category)
  {
    $this->provision_data = Arr::add($this->provision_data, 'provision_trademark_id', $trademark->id);
    $this->provision_data = Arr::add($this->provision_data, 'provision_type_id', $type->id);
    $this->provision_data = Arr::add($this->provision_data, 'measure_id', $measure->id);
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
   * crear un proveedor
   * @return Supplier
   */
  public function createSupplier(): Supplier
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
   * crear periodo de presupuestos
   * @return RequestForQuotationPeriod
   */
  public function createQuotationPeriod()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data += ['period_status_id' => $period_status->id];

    return RequestForQuotationPeriod::create($this->period_data);
  }

  /**
   * A basic feature test example.
   */
  public function test_trabajo_de_actualizacion_de_precios_mediante_presupuestos(): void
  {
    $trademark = $this->crearMarca();
    $provision_type = $this->crearTipo();
    $measure = $this->crearMedida();
    $category = $this->crearCategoriaDeSuministro($provision_type, $measure);

    // suministro y pack
    $provision = $this->crearSuministro($trademark, $provision_type, $measure, $category);
    $pack = $this->crearPack($provision);

    // proveedor
    $supplier = $this->createSupplier();

    // asociar provision y pack al supplier (pivot inicial)
    $supplier->provisions()->attach($provision->id, ['price' => 100]);
    $supplier->packs()->attach($pack->id, ['price' => 200]);

    // periodo presupuestario
    // datos minimos sin completar tabla intermedia con suministros y packs
    $period = $this->createQuotationPeriod();

    // crear un presupuesto respondido
    $quotation = Quotation::create([
      'quotation_code' => 'test000000001',
      'supplier_id' => $supplier->id,
      'period_id' => $period->id,
      'is_completed' => true,
    ]);

    $quotation->provisions()->attach($provision->id, [
      'unit_price' => 150,
      'has_stock' => true,
      'quantity' => 1,
      'total_price' => 150,
    ]);

    $quotation->packs()->attach($pack->id, [
      'unit_price' => 250,
      'has_stock' => true,
      'quantity' => 1,
      'total_price' => 250,
    ]);

    // ejecutar el job
    (new UpdateSuppliersPricesJob($period))->handle();

    // verificar que el precio del supplier se actualizo
    $this->assertEquals(150, $supplier->provisions()->find($provision->id)->pivot->price);
    $this->assertEquals(250, $supplier->packs()->find($pack->id)->pivot->price);
  }
}
