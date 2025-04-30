<?php

namespace App\Services\Stock;

use App\Models\Stock;
use App\Models\StockMovement;
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
          'movement_type' => 'elaboracion',
          'registered_at' => $stock_data['elaborated_at'] ?? now(),
        ]);

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
   * @return StockMovement
   * @throws Exception
   */
  public function registerMovement(int $stock_id, int $quantity, string $movement_type): StockMovement
  {
    try {
      return DB::transaction(function () use ($stock_id, $quantity, $movement_type) {
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
          'quantity'      => $quantity,
          'movement_type' => $movement_type,
          'registered_at' => now(),
        ]);
      });
    } catch (Exception $e) {
      throw new Exception("Error al registrar el movimiento: " . $e->getMessage());
    }
  }
}
