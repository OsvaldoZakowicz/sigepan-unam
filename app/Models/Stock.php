<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * * modelo stock de productos elaborados
 */
class Stock extends Model implements Auditable
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

  protected $fillable = [
    'product_id',     // producto asociado
    'recipe_id',      // receta de elaboracion asociada
    'lote_code',      // codigo de lote
    'quantity_total', // cantidad de productos elaborados para ese lote
    'quantity_left',  // cantidad de productos restantes para ese lote (calculado)
    'expired_at',     // fecha de vencimineto del lote
    'elaborated_at',  // fecha de elaboracion del lote
  ];

  /**
   * Los atributos que deben ser convertidos
   * @var array<string,string>
   */
  protected $casts = [
    'quantity_total' => 'integer',
    'quantity_left'  => 'integer',
    'expired_at'     => 'datetime',
    'elaborated_at'  => 'datetime',
  ];

  /**
   * Verificar si el stock ha expirado
   * verifica si la fecha de 'expired_at' es anterior a la actual
   * @return bool Retorna true si el stock ha expirado, false en caso contrario
   */
  public function getIsExpiredAttribute()
  {
    return $this->expired_at->toDateString() <= now()->toDateString();
  }

  // * un stock pertenece a un producto
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id', 'id');
  }

  // * un stock se elabora con una receta
  public function recipe(): BelongsTo
  {
    return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
  }

  // * un stock tiene muchos movimientos
  public function stock_movements(): HasMany
  {
    return $this->hasMany(StockMovement::class, 'stock_id', 'id');
  }

  // * un stock, al elaborarse, impacta en n existencias
  public function stock_existences(): HasMany
  {
    return $this->hasMany(Existence::class, 'stock_id', 'id');
  }
}
