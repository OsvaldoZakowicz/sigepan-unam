<?php

namespace Database\Seeders;

use App\Models\DatoTienda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatoTiendaSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // datos basicos de la tienda online
    $datos = [
      [
        'clave' => 'horario_atencion',
        'valor' => 'lunes a viernes de 8:00 a 12:00 hrs y de 16:30 a 20:30 hrs, sabados y domingos de 8:30 a 12:00 hrs',
        'descripcion' => 'Horarios de atención de la tienda'
      ],
      [
        'clave' => 'lugar_retiro_productos',
        'valor' => 'Avenida las Heras 1220 casi Flemming - Apóstoles - Misiones',
        'descripcion' => 'Donde retirar el pedido'
      ],
      [
        'clave' => 'tiempo_espera_pago',
        'valor' => 'Luego del pedido, esperamos el pago en las proximas 2 a 3 hrs',
        'descripcion' => 'Tiempo de espera para el pago del pedido'
      ]
    ];

    foreach ($datos as $dato) {
      DatoTienda::establecerValor(
        $dato['clave'],
        $dato['valor'],
        $dato['descripcion']
      );
    }
  }
}
