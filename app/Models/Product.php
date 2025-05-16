<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'product_name',
    'product_short_description',
    'product_expires_in',
    'product_in_store',
    'product_image_path',
  ];

  /**
   * Los atributos que deben ser convertidos
   * @var array<string,string>
   */
  protected $casts = [
    'product_expires_in' => 'integer',
    'product_in_store'   => 'boolean',
    'created_at'         => 'datetime',
    'updated_at'         => 'datetime',
    'deleted_at'         => 'datetime',
  ];

  /**
   * obtener atributo deleted_at, y presentarlo
   * deleted_at es una fecha o null, cuando tiene una fecha indica el borrado
   */
  protected function deletedAt(): Attribute
  {
    return Attribute::make(
      get: fn(string|null $value) => $value ? 'borrado' : 'activo'
    );
  }

  /**
   * Calcular el stock total disponible de este producto
   * sumatoria de cantidad restante
   * $product->total_stock
   */
  public function getTotalStockAttribute()
  {
    return $this->stocks()->sum('quantity_left');
  }

  //* un producto tiene muchos tags de producto
  public function tags(): BelongsToMany
  {
    return $this->belongsToMany(Tag::class, 'product_tag')
      ->withTimestamps();
  }

  //* un producto tiene muchas recetas
  public function recipes(): HasMany
  {
    return $this->hasMany(Recipe::class, 'product_id', 'id');
  }

  //* un producto tiene muchas ordenes
  public function orders(): BelongsToMany
  {
    return $this->belongsToMany(Order::class, 'order_product')
      ->withPivot('quantity', 'unit_price', 'subtotal_price')
      ->withTimestamps();
  }

  //* un producto tiene diversos stocks
  public function stocks(): HasMany
  {
    return $this->hasMany(Stock::class, 'product_id', 'id');
  }

  //* un producto esta en muchas ventas
  public function sales(): BelongsToMany
  {
    return $this->belongsToMany(Sale::class, 'product_sale')
      ->withPivot('sale_quantity', 'unit_price', 'subtotal_price', 'details')
      ->withTimestamps();
  }

  /**
   * Obtiene los precios asociados a este producto.
   */
  public function prices(): HasMany
  {
    return $this->hasMany(Price::class);
  }

  /**
   * Obtiene el precio predeterminado para este producto.
   */
  public function defaultPrice()
  {
    return $this->prices()->where('is_default', true)->first();
  }

  /**
   * Obtiene el precio apropiado para una cantidad específica.
   */
  public function getPriceForQuantity(int $quantity)
  {
    return Price::findPriceForQuantity($this->id, $quantity) ?? $this->defaultPrice();
  }

  /**
   * Agrega un nuevo precio para este producto.
   */
  public function addPrice(int $quantity, float $price, string $description, bool $isDefault = false): Price
  {
    $newPrice = $this->prices()->create([
      'quantity' => $quantity,
      'price' => $price,
      'description' => $description,
      'is_default' => $isDefault,
    ]);

    if ($isDefault) {
      $newPrice->setAsDefault();
    }

    return $newPrice;
  }
}
