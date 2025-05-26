<?php

namespace App\Livewire\Stats;

use App\Models\Sale;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class SalesStats extends Component
{

  #[Url]
  public $start_date = '';

  #[Url]
  public $end_date = '';

  /**
   * buscar ventas.
   */
  public function searchSales()
  {
    $sales = Sale::when(
      $this->start_date && $this->end_date,
      function ($query) {
        $query->where('sold_on', '>=', $this->start_date . ' 00:00:00')
          ->where('sold_on', '<=', $this->end_date . ' 23:59:59');
      }
    )
      ->with(['products']) // Cargamos los productos con la relación correcta
      ->get()
      ->groupBy(function ($sale) {
        return date('Y-m-d', strtotime($sale->sold_on));
      })
      ->map(function ($salesGroup) {
        $productTotals = [];
        foreach ($salesGroup as $sale) {
          foreach ($sale->products as $product) { // Usamos la relación products
            $productName = $product->product_name; // Usando el campo correcto del producto
            if (!isset($productTotals[$productName])) {
              $productTotals[$productName] = 0;
            }
            // Usamos los campos de la tabla pivot
            $productTotals[$productName] += $product->pivot->sale_quantity * $product->pivot->unit_price;
          }
        }
        return [
          'date' => date('d-m-Y', strtotime($salesGroup->first()->sold_on)),
          'products' => $productTotals,
          'daily_total' => $salesGroup->sum('total_price')
        ];
      });

    // Preparar datos para el gráfico
    $dates = $sales->pluck('date');
    $products = $sales->flatMap(fn($sale) => array_keys($sale['products']))->unique();
    $datasets = [];

    foreach ($products as $product) {
      $datasets[] = [
        'label' => $product,
        'data' => $sales->map(fn($sale) => $sale['products'][$product] ?? 0)->values()
      ];
    }

    $this->dispatch('make-chart', [
      'dates' => $dates,
      'datasets' => $datasets
    ]);

    return $sales;
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $sales = $this->searchSales();
    return view('livewire.stats.sales-stats', compact('sales'));
  }
}
