<?php

namespace App\Services\Stats;

use App\Models\Sale;
use Illuminate\Support\Collection;

class StatsSalesService
{
  /**
   * Buscar ventas usando Eloquent ORM
   * Solo se encarga de la consulta, sin procesamiento
   * @param string $start_date
   * @param string $end_date
   * @param string $product
   * @return Collection $sales
   */
  public function searchSales(string $start_date, string $end_date, string $product): Collection
  {
    return Sale::when(
      $start_date && $end_date,
      function ($query) use ($start_date, $end_date) {
        $query->whereBetween('sold_on', [
          $start_date . ' 00:00:00',
          $end_date . ' 23:59:59'
        ]);
      }
    )
      ->when(
        !$start_date && $end_date,
        function ($query) use ($start_date, $end_date) {
          $query->where('sold_on', '<=', $end_date . ' 23:59:59');
        }
      )
      ->when(
        $start_date && !$end_date,
        function ($query) use ($start_date, $end_date) {
          $query->where('sold_on', '>=', $start_date . ' 00:00:00');
        }
      )
      ->with(['products'])
      ->when($product, function ($query) use ($product) {
        $query->whereHas('products', function ($q) use ($product) {
          $q->where('products.id', $product);
        });
      })
      ->orderBy('sold_on', 'asc')
      ->get();
  }

  /**
   * Procesar ventas para mostrar en tabla
   * Agrupa por fecha y calcula totales por producto
   * @param Collection ventas filtradas
   * @return Collection $sales ventas procesadas en formato array
   */
  public function processSalesForTable(Collection $sales): Collection
  {
    return $sales
      ->groupBy(function ($sale) {
        return $sale->sold_on->format('Y-m-d');
      })
      ->map(function ($salesGroup) {
        $productTotals = []; //$
        $productQuantitySold = []; //cuanto vendio

        foreach ($salesGroup as $sale) {
          foreach ($sale->products as $product) {
            $productName = $product->product_name;

            if (!isset($productTotals[$productName])) {
              $productTotals[$productName] = 0;
            }

            $productTotals[$productName] +=
              $product->pivot->sale_quantity * $product->pivot->unit_price;

            if (!isset($productQuantitySold[$productName])) {
              $productQuantitySold[$productName] = 0;
            }

            // xtraer la cantidad del detalle (entre parentesis)
            preg_match('/\((\d+)\)/', $product->pivot->details, $matches);
            $unitsPerItem = (int)$matches[1];
            $productQuantitySold[$productName] += $unitsPerItem * $product->pivot->sale_quantity;
          }
        }

        return [
          'date' => $salesGroup->first()->sold_on->format('d-m-Y'),
          'products' => $productTotals,
          'quantity_sold' => $productQuantitySold,
          'daily_total' => $salesGroup->sum('total_price')
        ];
      });
  }

  /**
   * preparar datos especificamente para chart.js
   * @param Collection ventas procesadas
   * @return array para chart.js
   */
  public function prepareChartData(Collection $processedSales): array
  {
    // extraer todas las fechas (labels para eje X)
    $dates = $processedSales->pluck('date')->values()->toArray();

    // extraer todos los productos unicos
    $allProducts = $processedSales
      ->flatMap(fn($sale) => array_keys($sale['products']))
      ->unique()
      ->values();

    // crear datasets para cada producto
    $datasets = $allProducts->map(function ($product) use ($processedSales) {
      return [
        'label' => $product,
        'data' => $processedSales->map(function ($sale) use ($product) {
          return $sale['products'][$product] ?? 0;
        })->values()->toArray()
      ];
    })->toArray();

    return [
      'labels' => $dates,
      'datasets' => $datasets
    ];
  }

  /**
   * convertir datos procesados a formato plano para paginacion
   * y para PDF de estadisticas
   * cada fila representa: fecha + producto + cantidad + total
   * @param Collection ventas procesadas
   * @return Collection $flatData
   */
  public function flattenSalesData(Collection $processedSales): Collection
  {
    $flatData = collect();

    foreach ($processedSales as $sale) {
      foreach ($sale['products'] as $productName => $total) {
        $flatData->push([
          'date' => $sale['date'],
          'product' => $productName,
          'quantity_sold' => $sale['quantity_sold'][$productName],
          'total' => $total
        ]);
      }
    }

    return $flatData;
  }
}
