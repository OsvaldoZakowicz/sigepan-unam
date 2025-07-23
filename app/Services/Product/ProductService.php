<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
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
   * actualizar producto
   * @param array $productData Datos del producto
   * @param $product Producto a actualizar
   * @return Product
   */
  public function updateProduct(array $productData, Product $product): Product
  {
    return DB::transaction(function () use ($productData, $product) {

      // si el nombre del producto cambia, editar titulo de las recetas
      if ($productData['product_name'] !== $product->product_name) {
        foreach ($product->recipes as $recipe) {
          $recipe->recipe_title = 'receta de ' . $productData['product_name'] . ' por ' . $recipe->recipe_yields;
          $recipe->save();
        }
      }

      $product->product_name              = $productData['product_name'];
      $product->product_short_description = $productData['product_short_description'];
      $product->product_expires_in        = $productData['product_expires_in'];
      $product->product_in_store          = $productData['product_in_store'];
      $product->product_image_path        = $productData['product_image_path'];
      $product->save();

      // sincronizar tags
      $tags_to_sync = Arr::map($productData['tags_list'], function ($tag) { return $tag['tag']->id; });
      $product->tags()->sync($tags_to_sync);

      return $product;
    });
  }

  /**
   * actualizar precios, eliminando los antiguos
   */
  public function updateProductPrices(Product $product, array $pricesList): void
  {
    // eliminar precios existentes
    foreach ($product->prices as $price) {
      $price->delete();
    }

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

  /**
   * puedo editar ciertos aspectos del producto?
   * - No puedo editar el nombre del producto si este tiene ordenes,
   * ventas o stock asociado.
   * - Por que?: pierdo identidad si ya esta en uso. Por ejemplo:
   * vendi 100 pan casero y luego le cambio el nombre a bollo de membrillo.
   */
  public function isProductEditable(Product $product): bool
  {
    if ($product->orders()->count() > 0) {
      return false;
    }

    if ($product->sales()->count() > 0) {
      return false;
    }

    if ($product->stocks()->count() > 0) {
      return false;
    }

    return true;
  }

}
