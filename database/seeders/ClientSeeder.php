<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Gender;
use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // datos de ejemplo para los clientes
    $clientes = [
      [
        'user' => [
          'name' => 'maria',
          'email' => 'maria@cliente.com',
        ],
        'profile' => [
          'first_name' => 'maria',
          'last_name' => 'gonzalez',
          'dni' => '28456123',
          'birthdate' => '1980-05-15',
          'phone_number' => '3764111222',
          'gender' => 'femenino'
        ],
        'address' => [
          'street' => 'san martin',
          'number' => '2534',
          'postal_code' => '3350',
          'city' => 'apostoles'
        ]
      ],
      [
        'user' => [
          'name' => 'juan',
          'email' => 'juan@cliente.com',
        ],
        'profile' => [
          'first_name' => 'juan',
          'last_name' => 'perez',
          'dni' => '30789456',
          'birthdate' => '1985-08-22',
          'phone_number' => '3764333444',
          'gender' => 'masculino'
        ],
        'address' => [
          'street' => 'belgrano',
          'number' => '1785',
          'postal_code' => '3350',
          'city' => 'apostoles'
        ]
      ],
      [
        'user' => [
          'name' => 'carolina',
          'email' => 'carolina@cliente.com',
        ],
        'profile' => [
          'first_name' => 'carolina',
          'last_name' => 'rodriguez',
          'dni' => '33159789',
          'birthdate' => '1988-11-30',
          'phone_number' => '3764555666',
          'gender' => 'femenino'
        ],
        'address' => [
          'street' => 'cordoba',
          'number' => '3456',
          'postal_code' => '3350',
          'city' => 'apostoles'
        ]
      ]
    ];

    foreach ($clientes as $cliente) {
      // 1. crear usuario
      $user = User::create([
        'name'              => $cliente['user']['name'],
        'email'             => $cliente['user']['email'],
        'email_verified_at' => now(env('APP_TIMEZONE')),
        'password'          => bcrypt('12345678'),
        'is_first_login'    => false,
      ]);

      // asignar rol de cliente
      $user->assignRole('cliente');

      // 2. crear direccion
      $address = Address::create($cliente['address']);

      // 3. obtener el id del genero
      $gender = Gender::where('gender', $cliente['profile']['gender'])->first();

      // 4. crear perfil asociado al usuario, direccion y genero
      Profile::create([
        'first_name'   => $cliente['profile']['first_name'],
        'last_name'    => $cliente['profile']['last_name'],
        'dni'          => $cliente['profile']['dni'],
        'birthdate'    => $cliente['profile']['birthdate'],
        'phone_number' => $cliente['profile']['phone_number'],
        'gender_id'    => $gender->id,
        'user_id'      => $user->id,
        'address_id'   => $address->id,
      ]);
    }
  }
}
