<?php

namespace Database\Seeders;

use App\Models\DatoNegocio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatoNegocioSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // datos basicos del negocio
    $datos = [
      [
        'clave' => 'razon_social',
        'valor' => 'Panaderia los Hermanos S.R.L',
        'descripcion' => 'Nombre legal del negocio'
      ],
      [
        'clave' => 'nombre_comercial',
        'valor' => 'Panaderia los Hermanos',
        'descripcion' => 'Nombre de fantasía'
      ],
      [
        'clave' => 'cuit',
        'valor' => '30562113289',
        'descripcion' => 'CUIT del negocio'
      ],
      [
        'clave' => 'domicilio',
        'valor' => 'Avenida las Heras 1220 casi Flemming - Apóstoles - Misiones',
        'descripcion' => 'Dirección fiscal'
      ],
      [
        'clave' => 'condicion_iva',
        'valor' => 'iva responsable inscripto',
        'descripcion' => 'Condición frente al IVA'
      ],
      [
        'clave' => 'ingresos_brutos',
        'valor' => '',
        'descripcion' => 'Número de ingresos brutos'
      ],
      [
        'clave' => 'inicio_actividades',
        'valor' => '01-01-2001',
        'descripcion' => 'Fecha de inicio de actividades'
      ],
      [
        'clave' => 'punto_venta',
        'valor' => '',
        'descripcion' => 'Punto de venta autorizado por AFIP'
      ],
      [
        'clave' => 'telefono',
        'valor' => '3765073022',
        'descripcion' => 'Teléfono de contacto'
      ],
      [
        'clave' => 'email',
        'valor' => 'panaderialoshermanos@gmail.com',
        'descripcion' => 'Email de contacto'
      ],
    ];

    foreach ($datos as $dato) {
      DatoNegocio::establecerValor(
        $dato['clave'],
        $dato['valor'],
        $dato['descripcion']
      );
    }
  }
}
