<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{

  use WithoutModelEvents;

  /**
   * Sembrar la base de datos.
   * @return void
   */
  public function run(): void
  {
    // CUITS validos para proveedores
    $cuits = ['30547339416', '33546700939', '30532708059', '30546741253'];

    // Datos de ejemplo para los proveedores
    $proveedores = [
      [
        'company' => 'molinos rio de la plata sa',
        'iva' => 'iva responsable inscripto',
        'phone' => '3764555111',
        'address' => [
          'street' => 'uruguay',
          'number' => '4075',
          'postal_code' => '3300',
          'city' => 'posadas'
        ]
      ],
      [
        'company' => 'distribuidora del norte srl',
        'iva' => 'iva responsable inscripto',
        'phone' => '3764555222',
        'address' => [
          'street' => 'bolivar',
          'number' => '2233',
          'postal_code' => '3300',
          'city' => 'posadas'
        ]
      ],
      [
        'company' => 'arcor saic',
        'iva' => 'iva responsable inscripto',
        'phone' => '3764555333',
        'address' => [
          'street' => 'lavalle',
          'number' => '1556',
          'postal_code' => '3300',
          'city' => 'posadas'
        ]
      ],
      [
        'company' => 'la serenisima sa',
        'iva' => 'iva responsable inscripto',
        'phone' => '3764555444',
        'address' => [
          'street' => 'junin',
          'number' => '3242',
          'postal_code' => '3300',
          'city' => 'posadas'
        ]
      ]
    ];

    foreach ($cuits as $index => $cuit) {
      // 1. Crear usuario
      $user = User::create([
        'name'              => $cuit,
        'email'             => "proveedor{$index}@empresa.com",
        'email_verified_at' => now(env('APP_TIMEZONE')),
        'password'          => bcrypt('12345678'),
        'is_first_login'    => false,
      ]);

      // asignar rol de proveedor
      $user->assignRole('proveedor');

      // 2. crear direccion
      $address = Address::create($proveedores[$index]['address']);

      // 3. crear proveedor
      Supplier::create([
        'company_name'       => $proveedores[$index]['company'],
        'company_cuit'       => $cuit,
        'iva_condition'      => $proveedores[$index]['iva'],
        'phone_number'       => $proveedores[$index]['phone'],
        'short_description'  => "Proveedor de insumos para panadería",
        'status_is_active'   => true,
        'status_description' => 'proveedor activo',
        'status_date'        => now(),
        'user_id'            => $user->id,
        'address_id'         => $address->id
      ]);
    }
  }
}
