<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatoNegocio extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'datos_negocio';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'clave',
    'valor',
    'descripcion',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Get a specific business data by key.
   *
   * @param string $key
   * @return string|null
   */
  public static function obtenerValor(string $key): ?string
  {
    $dato = self::where('clave', $key)->first();
    return $dato ? $dato->valor : null;
  }

  /**
   * Set a specific business data by key.
   *
   * @param string $key
   * @param string $value
   * @param string|null $description
   * @return \App\Models\DatoNegocio
   */
  public static function establecerValor(string $key, string $value, ?string $description = null): self
  {
    return self::updateOrCreate(
      ['clave' => $key],
      [
        'valor' => $value,
        'descripcion' => $description,
      ]
    );
  }

  /**
   * Get all business data as key-value array.
   *
   * @return array<string, string>
   */
  public static function obtenerTodos(): array
  {
    return self::pluck('valor', 'clave')->toArray();
  }
}
