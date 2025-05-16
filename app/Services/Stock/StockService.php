<?php

namespace App\Services\Stock;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Existence;
use App\Models\Recipe;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
  /**
   * Generar un código de lote único
   * @return string
   * @throws Exception
   */
  private function generateUniqueLoteCode()
  {
    $maxAttempts = 10;
    $attempt = 0;

    do {
      // Generar código aleatorio
      $code = 'LT_' . Str::upper(Str::random(8));

      // Verificar si existe
      $exists = Stock::where('lote_code', $code)->exists();

      $attempt++;

      // Si no existe, retornamos el código
      if (!$exists) {
        return $code;
      }

      // Si después de varios intentos no encontramos un código único, lanzamos error
      if ($attempt >= $maxAttempts) {
        throw new Exception("No se pudo generar un código de lote único");
      }
    } while ($exists);
  }


  /**
   * Crear un nuevo stock con su movimiento inicial
   * @param array $stock_data Datos del stock a crear
   * @param int $initial_quantity Cantidad inicial del stock
   * @return Stock
   * @throws Exception
   */
  public function createStock(array $stock_data, int $initial_quantity): Stock
  {
    try {

      return DB::transaction(function () use ($stock_data, $initial_quantity) {

        // verificar existencias para la receta
        $recipe = Recipe::findOrFail($stock_data['recipe_id']);
        $provisions_check = $this->checkRecipeProvisions($recipe);

        if ($provisions_check !== true) {
          $missing = collect($provisions_check)
            ->map(fn($item) => "{$item['category']}: necesita {$item['required']}, disponible {$item['available']}")
            ->join("\n");

          throw new Exception("No hay suficientes existencias:\n" . $missing);
        }

        // Generar código de lote único
        $lote_code = $this->generateUniqueLoteCode();

        // Crear el stock
        $stock = Stock::create([
          'product_id'     => $stock_data['product_id'],
          'recipe_id'      => $stock_data['recipe_id'],
          'lote_code'      => $lote_code,
          'quantity_total' => $initial_quantity,
          'quantity_left'  => $initial_quantity,
          'expired_at'     => $stock_data['expired_at'],
          'elaborated_at'  => $stock_data['elaborated_at'] ?? now(),
        ]);

        // Crear el movimiento inicial
        $stock->stock_movements()->create([
          'quantity'      => $initial_quantity,
          'movement_type' => StockMovement::MOVEMENT_TYPE_ELABORACION(),
          'registered_at' => $stock_data['elaborated_at'] ?? now(),
        ]);

        // consumir las existencias necesarias
        $this->consumeProvisions($recipe, $stock);

        return $stock;
      });
    } catch (Exception $e) {

      throw new Exception("Error al crear el stock: " . $e->getMessage());
    }
  }

  /**
   * Registrar un movimiento de stock
   * @param int $stock_id ID del stock
   * @param int $quantity Cantidad del movimiento (negativo para salidas)
   * @param string $movement_type Tipo de movimiento
   * @param string $movement_reference_id id de referencia (stock, venta, otro...)
   * @param string $movement_reference_type modelo de referencia (stock, venta, otro...)
   * @return StockMovement
   * @throws Exception
   */
  public function registerMovement(
    int $stock_id,
    int $quantity,
    string $movement_type,
    string $movement_reference_id,
    string $movement_reference_type
  ): StockMovement {
    try {
      return DB::transaction(function () use (
        $stock_id,
        $quantity,
        $movement_type,
        $movement_reference_id,
        $movement_reference_type
      ) {

        $stock = Stock::findOrFail($stock_id);

        // Verificar que haya suficiente stock para movimientos negativos
        if ($quantity < 0 && abs($quantity) > $stock->quantity_left) {
          throw new Exception("No hay suficiente stock disponible");
        }

        // Actualizar la cantidad restante
        $stock->quantity_left += $quantity;
        $stock->save();

        // Registrar el movimiento
        return $stock->stock_movements()->create([
          'quantity'                => $quantity,
          'movement_type'           => $movement_type,
          'registered_at'           => now(),
          'movement_reference_id'   => $movement_reference_id,
          'movement_reference_type' => $movement_reference_type,
        ]);
      });
    } catch (Exception $e) {
      throw new Exception("Error al registrar el movimiento: " . $e->getMessage());
    }
  }

  /**
   * Verifica si hay suficientes existencias para elaborar una receta
   * @param Recipe $recipe
   * @return bool|array Retorna true si hay suficientes existencias, o un array con las categorías faltantes
   * @throws Exception
   */
  private function checkRecipeProvisions(Recipe $recipe): bool|array
  {
    // obtener las categorias y cantidades requeridas por la receta
    $required_categories_for_recipe = $recipe->provision_categories()
      ->with('measure')
      ->withPivot('quantity')
      ->get();

    $missing_categories = [];

    // por cada categoria requerida
    foreach ($required_categories_for_recipe as $category_for_recipe) {

      // sumar todas las existencias de provisiones en esta categoria
      $total_existence = Existence::whereHas('provision', function ($query) use ($category_for_recipe) {
        $query->where('provision_category_id', $category_for_recipe->id);
      })->sum('quantity_amount');

      // si no hay suficiente existencia para esta categoria
      if ($total_existence < $category_for_recipe->pivot->quantity) {
        $missing_categories[] = [
          'category'  => $category_for_recipe->provision_category_name,
          'required'  => convert_measure($category_for_recipe->pivot->quantity, $category_for_recipe->measure),
          'available' => convert_measure($total_existence, $category_for_recipe->measure)
        ];
      }
    }

    return empty($missing_categories) ? true : $missing_categories;
  }

  /**
   * Consume suministros necesarios en la elaboracion del stock
   * a partir de una receta.
   * @param Recipe $recipe
   * @param Stock $stock
   * @return void
   */
  private function consumeProvisions(Recipe $recipe, Stock $stock): void
  {
    // mensaje de error
    $exception_msg = "No se pudo consumir toda la cantidad requerida para la categoría";

    DB::transaction(function () use ($recipe, $stock, $exception_msg) {

      // categorias de suministros que requiere la receta
      $required_categories = $recipe->provision_categories()
        ->withPivot('quantity')
        ->get();

      // por cada categoria requerida en la receta
      foreach ($required_categories as $category) {
        $remaining_quantity = $category->pivot->quantity;

        // obtener existencias ordenadas por fecha mas antigua
        $existences = Existence::whereHas('provision', function ($query) use ($category) {
          $query->where('provision_category_id', $category->id);
        })->where('quantity_amount', '>', 0)
          ->orderBy('registered_at', 'asc')
          ->get();

        // por cada existencia de la categoria
        foreach ($existences as $existence) {
          if ($remaining_quantity <= 0) break;

          $quantity_to_consume = min($existence->quantity_amount, $remaining_quantity);

          // crear nuevo registro de existencia negativo
          Existence::create([
            'provision_id'    => $existence->provision_id,
            'stock_id'        => $stock->id,
            'quantity_amount' => -$quantity_to_consume, //negativo
            'movement_type'   => Existence::MOVEMENT_TYPE_ELABORACION(),
            'registered_at'   => now(),

          ]);

          $remaining_quantity -= $quantity_to_consume;
        }

        if ($remaining_quantity > 0) {
          throw new Exception("{$exception_msg} {$category->provision_category_name}");
        }
      }
    });
  }
}
