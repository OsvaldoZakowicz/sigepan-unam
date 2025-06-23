<?php

namespace App\Livewire\Stats;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class SalesStats extends Component
{
  #[Url]
  public $start_date = '';

  #[Url]
  public $end_date = '';

  #[Url]
  public $product = '';
  public $all_products = [];

  public $currentPage = 1;
  public $perPage = 10;

  /**
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    $this->all_products = Product::all();
  }

  /**
   * Buscar ventas usando Eloquent ORM
   * Solo se encarga de la consulta, sin procesamiento
   */
  private function searchSales(): Collection
  {
    return Sale::when(
      $this->start_date && $this->end_date,
      function ($query) {
        $query->whereBetween('sold_on', [
          $this->start_date . ' 00:00:00',
          $this->end_date . ' 23:59:59'
        ]);
      }
    )
      ->with(['products'])
      ->when($this->product, function ($query) {
        $query->whereHas('products', function ($q) {
          $q->where('products.id', $this->product);
        });
      })
      ->orderBy('sold_on', 'asc')
      ->get();
  }

  /**
   * Procesar ventas para mostrar en tabla
   * Agrupa por fecha y calcula totales por producto
   */
  private function processSalesForTable(Collection $sales): Collection
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
   */
  private function prepareChartData(Collection $processedSales): array
  {
    // Extraer todas las fechas (labels para eje X)
    $dates = $processedSales->pluck('date')->values()->toArray();

    // Extraer todos los productos únicos
    $allProducts = $processedSales
      ->flatMap(fn($sale) => array_keys($sale['products']))
      ->unique()
      ->values();

    // Crear datasets para cada producto
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
   * cada fila representa: fecha + producto + total
   */
  private function flattenSalesData(Collection $processedSales): Collection
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

  /**
   * paginar datos planos
   */
  private function paginateFlatData(Collection $flatData, int $perPage = 10): array
  {
    $totalItems = $flatData->count();
    $totalPages = $totalItems > 0 ? ceil($totalItems / $perPage) : 1;
    $this->currentPage = max(1, min($this->currentPage, $totalPages));

    $offset = ($this->currentPage - 1) * $perPage;
    $paginatedItems = $flatData->slice($offset, $perPage);

    return [
      'items' => $paginatedItems,
      'totalItems' => $totalItems,
      'totalPages' => $totalPages,
      'currentPage' => $this->currentPage,
      'hasNextPage' => $this->currentPage < $totalPages,
      'hasPrevPage' => $this->currentPage > 1,
      'startItem' => $totalItems > 0 ? $offset + 1 : 0,
      'endItem' => $totalItems > 0 ? min($offset + $perPage, $totalItems) : 0
    ];
  }

  /**
   * limpiar filtros de busqueda y resetear paginacion
   */
  public function clearFilters(): void
  {
    $this->start_date = '';
    $this->end_date = '';
    $this->product = '';
    $this->currentPage = 1;
  }

  /**
   * navegacion de paginacion
   */
  public function nextPage(): void
  {
    $this->currentPage++;
  }

  public function prevPage(): void
  {
    if ($this->currentPage > 1) {
      $this->currentPage--;
    }
  }

  public function goToPage(int $page): void
  {
    $this->currentPage = max(1, $page);
  }

  /**
   * resetear paginacion cuando cambian los filtros
   */
  public function updated($propertyName): void
  {
    if (in_array($propertyName, ['start_date', 'end_date'])) {
      $this->currentPage = 1;
    }
  }

  /**
   * Renderizar vista
   */
  public function render(): View
  {
    // 1. Buscar ventas (solo consulta)
    $rawSales = $this->searchSales();

    // 2. Procesar para gráfico (TODOS los datos)
    $processedSales = $this->processSalesForTable($rawSales);

    // 3. Preparar datos para Chart.js (TODOS los datos)
    $chartData = $this->prepareChartData($processedSales);

    // 4. Preparar datos para tabla (con paginación)
    $flatData = $this->flattenSalesData($processedSales);
    $paginationData = $this->paginateFlatData($flatData, $this->perPage);

    //dd($processedSales, $flatData, $chartData);

    // 5. Enviar datos al frontend
    $this->dispatch('make-chart', $chartData);

    return view('livewire.stats.sales-stats', [
      'sales' => $paginationData['items'],
      'totalItems' => $paginationData['totalItems'],
      'totalPages' => $paginationData['totalPages'],
      'currentPage' => $paginationData['currentPage'],
      'hasNextPage' => $paginationData['hasNextPage'],
      'hasPrevPage' => $paginationData['hasPrevPage'],
      'startItem' => $paginationData['startItem'],
      'endItem' => $paginationData['endItem']
    ]);
  }
}
