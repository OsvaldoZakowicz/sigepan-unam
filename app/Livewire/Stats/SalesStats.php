<?php

namespace App\Livewire\Stats;

use App\Models\Product;
use App\Models\Sale;
use App\Services\Stats\StatsSalesService;
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
    $sss = new StatsSalesService();
    return $sss->searchSales($this->start_date, $this->end_date, $this->product);
  }

  /**
   * Procesar ventas para mostrar en tabla
   * Agrupa por fecha y calcula totales por producto
   */
  private function processSalesForTable(Collection $sales): Collection
  {
    $sss = new StatsSalesService();
    return $sss->processSalesForTable($sales);
  }

  /**
   * preparar datos especificamente para chart.js
   */
  private function prepareChartData(Collection $processedSales): array
  {
    $sss = new StatsSalesService();
    return $sss->prepareChartData($processedSales);
  }

  /**
   * convertir datos procesados a formato plano para paginacion
   * cada fila representa: fecha + producto + total
   */
  private function flattenSalesData(Collection $processedSales): Collection
  {
    $sss = new StatsSalesService();
    return $sss->flattenSalesData($processedSales);
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
   * abrir pdf en una nueva pestaña,
   * para poder visualizar y descargar.
   * @return void
   */
  public function openPdfStat(): void
  {
    // generar URL para ver el pdf
    $pdfUrl = route('open-pdf-stat', []) . '?' . http_build_query([
      'start_date' => $this->start_date,
      'end_date'   => $this->end_date,
      'product'    => $this->product,
    ]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
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
