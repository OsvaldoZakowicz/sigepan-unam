<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatoTienda extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'datos_tienda';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'clave',
    'valor',
    'descripcion'
  ];

  /**
   * obtener valor de configuracion
   * @param string $clave
   * @return string|null
   */
  public static function obtenerValor(string $clave): ?string
  {
    $dato = self::where('clave', $clave)->first();
    return $dato ? $dato->valor : null;
  }

  /**
   * establecer valor de configuracion
   * @param string $clave
   * @param string $valor
   * @param string $descripcion
   * @return void
   */
  public static function establecerValor(string $clave, string $valor, string $descripcion): void
  {
    self::updateOrCreate(
      ['clave' => $clave],
      [
        'valor' => $valor,
        'descripcion' => $descripcion
      ]
    );
  }

  /**
   * obtener todos los valores
   * @return array<string, string>
   */
  public static function obtenerTodos(): array
  {
    return self::pluck('valor', 'clave')->toArray();
  }
}
