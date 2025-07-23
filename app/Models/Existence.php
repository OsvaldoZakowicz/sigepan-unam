<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Existence extends Model implements Auditable
{
  use HasFactory;
  use \OwenIt\Auditing\Auditable;

  /**
   * Eventos que deben ser auditados
   */
  protected $auditEvents = [
    'created',
    'updated', 
    'deleted',
  ];

  /**
   * Tipos de movimientos disponibles
   * * movement_type
   * compra - (suma existencias)
   * elaboracion - (resta existencias )
   * perdida - (resta existencias)
   */
  protected static $MOVEMENT_TYPE_COMPRA = 'compra';
  protected static $MOVEMENT_TYPE_ELABORACION = 'elaboracion';
  protected static $MOVEMENT_TYPE_PERDIDA = 'perdida';

  /**
   * id de suministro (packs son suministros agrupados)
   * id de compra (puede ser null)
   * id de stock (puede ser null)
   * tipo movimiento
   * fecha de registro
   * cantidad en existencias AFECTADAS (decimal: Kg, g, L, mL, ...) (puede ser negativo)
   */
  protected $fillable = [
    'provision_id',
    'purchase_id',
    'stock_id',
    'movement_type',
    'registered_at',
    'quantity_amount',
  ];

  /**
   * atributos que deben castearse
   */
  protected $casts = [
    'registered_at'   => 'timestamp',
    'quantity_amount' => 'decimal:2',
  ];

  /**
   * Retorna el tipo de movimiento compra
   */
  public static function MOVEMENT_TYPE_COMPRA(): string
  {
    return self::$MOVEMENT_TYPE_COMPRA;
  }

  /**
   * Retorna el tipo de movimiento elaboración
   */
  public static function MOVEMENT_TYPE_ELABORACION(): string
  {
    return self::$MOVEMENT_TYPE_ELABORACION;
  }

  /**
   * Retorna el tipo de movimiento pérdida
   */
  public static function MOVEMENT_TYPE_PERDIDA(): string
  {
    return self::$MOVEMENT_TYPE_PERDIDA;
  }

  // * un registro de existencias puede ser resultado de una compra
  public function purchase(): BelongsTo
  {
    return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
  }

  // * un registro de existencias puede ser resultado de un stock
  public function stock(): BelongsTo
  {
    return $this->belongsTo(Stock::class, 'stock_id', 'id');
  }

  // * un registro de existencias es de un suministro
  public function provision(): BelongsTo
  {
    return $this->belongsTo(Provision::class, 'provision_id', 'id');
  }
}
