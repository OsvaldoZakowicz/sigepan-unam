<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
  use HasFactory;

  /**
   * Los atributos que son asignables masivamente.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'product_id',
    'quantity',
    'price',
    'description',
    'is_default',
  ];

  /**
   * Los atributos que deben ser convertidos a tipos nativos.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'quantity' => 'integer',
    'price' => 'decimal:2',
    'is_default' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Obtiene el producto al que pertenece este precio.
   */
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  /**
   * Establece este precio como el predeterminado para el producto.
   * Actualiza todos los demás precios del mismo producto como no predeterminados.
   */
  public function setAsDefault(): void
  {
    // Marcar todos los precios del mismo producto como no predeterminados
    self::where('product_id', $this->product_id)
      ->where('id', '!=', $this->id)
      ->update(['is_default' => false]);

    // Establecer este precio como predeterminado
    if (!$this->is_default) {
      $this->is_default = true;
      $this->save();
    }
  }

  /**
   * Encuentra el precio adecuado para una cantidad específica de productos.
   * Devuelve el precio más cercano menor o igual a la cantidad dada.
   */
  public static function findPriceForQuantity(int $productId, int $quantity): ?self
  {
    return self::where('product_id', $productId)
      ->where('quantity', '<=', $quantity)
      ->orderByDesc('quantity')
      ->first();
  }
}
