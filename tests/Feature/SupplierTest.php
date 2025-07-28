<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;

class SupplierTest extends TestCase
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
    'status_date' => '2021-10-01 00:00:00'
  ];

  /**
   * @testCase TC001.
   * @purpose Crear un proveedor.
   * @expectedResult Se crea un proveedor en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_crear_proveedor()
  {
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];

    $supplier = Supplier::create($this->supplier_data);

    $this->assertDatabaseHas('users', ['name' => 'user', 'email' => 'user@mail.com']);
    $this->assertDatabaseHas('addresses', $this->address_data);
    $this->assertDatabaseHas('suppliers', $this->supplier_data);

    // existen los modelos en la BD
    $this->assertModelExists($user);
    $this->assertModelExists($addres);
    $this->assertModelExists($supplier);
  }

   /**
   * @testCase TC002.
   * @purpose Eliminar un proveedor.
   * @expectedResult Se elimina un proveedor en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_eliminar_proveedor()
  {
    $user = User::create($this->user_data);
    $address = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $address->id
    ];

    $supplier = Supplier::create($this->supplier_data);

    $supplier->delete();
    $supplier->user?->delete();
    $supplier->address?->delete();

    $this->assertSoftDeleted($supplier);
    $this->assertSoftDeleted($user);
    $this->assertSoftDeleted($address);
  }

}
