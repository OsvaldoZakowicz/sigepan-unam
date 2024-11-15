<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Address;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\Supplier;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Tests\TestCase;

class ProvisionSupplierTest extends TestCase
{
  use RefreshDatabase;

  //* preparar datos
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

  public $trademark_data = [
    'provision_trademark_name' => 'ledesma',
    'provision_trademark_is_editable' => true,
  ];

  public $provision_type_data = [
    'provision_type_name' => 'ingrediente',
    'provision_type_short_description' => 'description',
    'provision_type_is_editable' => true,
  ];

  public $measure_data = [
    'measure_name'              => 'kilogramos',
    'measure_abrv'              => 'kg',
    'measure_base'              => 1000,
    'measure_short_description' => 'mil gramos',
    'measure_is_editable'       => true,
  ];

  public $provision_data = [
    'provision_name' => 'azucar',
    'provision_quantity' => '1',
    'provision_short_description' => 'description',
  ];

  /**
   * * para un proveedor, asignarle un suministro con un precio
   */
  public function test_asignar_suministro_con_precio_al_proveedor()
  {
    //* probar
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);
    $trademark = ProvisionTrademark::create($this->trademark_data);
    $provision_type = ProvisionType::create($this->provision_type_data);
    $measure = Measure::create($this->measure_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id,
    ];

    $this->provision_data += [
      'provision_trademark_id' => $trademark->id,
      'provision_type_id' => $provision_type->id,
      'measure_id' => $measure->id
    ];

    // proveedor
    $supplier = Supplier::create($this->supplier_data);

    // suministro
    $provision = Provision::create($this->provision_data);

    // dar al proveedor un suministro con precio
    $supplier->provisions()->attach($provision->id, ['price' => 1200]);

    //* afirmar cambios
    // existen los registros en la BD
    // debido a la encriptacion de la contrasenia, uso el array de datos sin password
    $this->assertDatabaseHas('users', ['name' => 'user', 'email' => 'user@mail.com']);
    $this->assertDatabaseHas('addresses', $this->address_data);
    $this->assertDatabaseHas('suppliers', $this->supplier_data);
    $this->assertDatabaseHas('provision_trademarks', $this->trademark_data);
    $this->assertDatabaseHas('provision_types', $this->provision_type_data);
    $this->assertDatabaseHas('measures', $this->measure_data);
    $this->assertDatabaseHas('provisions', $this->provision_data);

    // el proveedor tiene el suministro
    $this->assertDatabaseHas('provision_supplier', [
      'supplier_id' => $supplier->id,
      'provision_id' => $provision->id,
      'price' => 1200
    ]);

  }
}
