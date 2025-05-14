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
}
