<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
  /**
   * Crear un nuevo producto con sus precios
   * @param array $productData Datos del producto
   * @param array $pricesList Lista de precios
   * @return Product
   */
  public function createProduct(array $productData, array $pricesList): Product
  {
    return DB::transaction(function () use ($productData, $pricesList) {

      // Validar que no haya cantidades duplicadas
      $quantities = collect($pricesList)->pluck('quantity');
      if ($quantities->count() !== $quantities->unique()->count()) {
        throw new \Exception('No pueden existir cantidades duplicadas en los precios.');
      }

      // Crear el producto
      $product = Product::create([
        'product_name' => $productData['product_name'],
        'product_short_description' => $productData['product_short_description'],
        'product_expires_in' => $productData['product_expires_in'],
        'product_in_store' => $productData['product_in_store'],
        'product_image_path' => $productData['product_image_path'],
      ]);

      // Asignar tags
      if (isset($productData['tags_list'])) {
        $tagIds = collect($productData['tags_list'])->pluck('tag.id');
        $product->tags()->attach($tagIds);
      }

      // Crear los precios
      foreach ($pricesList as $priceData) {
        try {
          $product->addPrice(
            quantity: $priceData['quantity'],
            price: $priceData['price'],
            description: $priceData['description'],
            isDefault: $priceData['is_default'] ?? false
          );
        } catch (\Illuminate\Database\QueryException $e) {
          // Si hay un error de duplicado, lanzar una excepción más amigable
          if ($e->errorInfo[1] === 1062) { // Error MySQL de duplicado
            throw new \Exception('No se pueden crear precios con cantidades duplicadas para el mismo producto.');
          }
          throw $e;
        }
      }

      return $product;
    });
  }

  /**
   * actualizar precios, eliminando los antiguos
   */
  public function updateProductPrices(Product $product, array $pricesList): void
  {
    // eliminar precios existentes
    $product->prices()->delete();

    // erear nuevos precios
    foreach ($pricesList as $priceData) {
      $product->prices()->create([
        'quantity' => $priceData['quantity'],
        'price' => $priceData['price'],
        'description' => $priceData['description'],
        'is_default' => $priceData['is_default'],
      ]);
    }
  }

  /**
   * el producto esta asociado a ventas?
   */
  public function isOnSales(Product $product): bool
  {
    $count_on_sales = $product->sales()->count();

    return ($count_on_sales > 0) ? true : false;
  }

  /**
   * el producto esta asociado a ordenes?
   */
  public function isOnOrders(Product $product): bool
  {
    $count_on_orders = $product->orders()->count();

    return ($count_on_orders > 0) ? true : false;
  }
}
