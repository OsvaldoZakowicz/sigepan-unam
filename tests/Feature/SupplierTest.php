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

  //* preparar datos de prueba
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
    'short_description' => 'description'
  ];

  /**
   * * prueba crear proveedor
   * debe existir la tabla en la base de datos
   * debo poder insertar un registro completo con usuario y direccion
   */
  public function test_crear_proveedor()
  {
    //* probar
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += ['user_id' => $user->id, 'address_id' => $addres->id];
    $supplier = Supplier::create($this->supplier_data);

    //* afirmar cambios
    // existen los registros en la BD
    // debido a la encriptacion de la contrasenia, uso el array de datos sin password
    $this->assertDatabaseHas('users', ['name' => 'user', 'email' => 'user@mail.com']);
    $this->assertDatabaseHas('addresses', $this->address_data);
    $this->assertDatabaseHas('suppliers', $this->supplier_data);

    // existen los modelos en la BD
    $this->assertModelExists($user);
    $this->assertModelExists($addres);
    $this->assertModelExists($supplier);
  }

  /**
   * * prueba eliminar proveedor
   * eliminar de la base de datos el provedor, usuario y direccion
   */
  public function test_eliminar_proveedor()
  {
    //* probar
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += ['user_id' => $user->id, 'address_id' => $addres->id];
    $supplier = Supplier::create($this->supplier_data);

    // borrar registros
    $supplier->delete();
    $user->delete();
    $addres->delete();

    //* afirmar cambios
    // NO existen los registros en la BD
    $this->assertDatabaseMissing('suppliers', $this->supplier_data);
    $this->assertDatabaseMissing('users', $this->user_data);
    $this->assertDatabaseMissing('addresses', $this->address_data);
  }

  /**
   * * editar un proveedor
   * editar en la base de datos algunos datos de proveedor
   * usar las relaciones user y address para editar
   */
  public function test_editar_proveedor()
  {
    //* probar
    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);
    $this->supplier_data += ['user_id' => $user->id, 'address_id' => $addres->id];
    $supplier = Supplier::create($this->supplier_data);

    // editar telefono
    $supplier->phone_number = '9999999999';
    $supplier->save();

    // editar usuario, pasando por proveedor
    $user_edit = $supplier->user;
    $user_edit->name = "pepe";
    $user_edit->save();

    // editar direccion, pasando por proveedor
    $address_edit = $supplier->address;
    $address_edit->street = "las heras";
    $address_edit->save();

    //* afirmar cambios
    // El proveedor se actualizo con otro telefono, nombre de usuario y calle
    $this->assertDatabaseHas('suppliers', ['phone_number' => '9999999999']);
    $this->assertDatabaseHas('users', ['name' => 'pepe']);
    $this->assertDatabaseHas('addresses', ['street' => 'las heras']);
  }
}
