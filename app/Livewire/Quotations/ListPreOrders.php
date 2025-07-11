<?php

namespace App\Livewire\Quotations;

use App\Models\Supplier;
use App\Models\PreOrder;
use App\Services\Supplier\PreOrderPeriodService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * LISTA DE PREORDENES PARA UN PROVEEDOR
 * SECCION DE UN PROVEEDOR
 */
class ListPreOrders extends Component
{
  use WithPagination;

  #[Url]
  public string $search_word = '';

  #[Url]
  public $preorder_status = '';

  #[Url]
  public $period_status = '';

  #[Url]
  public $period_start_at = '';

  #[Url]
  public $period_end_at = '';

  public Supplier $supplier;

  // estados del periodo de pre orden
  public int $status_open;
  public int $status_closed;

  // estados de la pre orden
  public string $status_pending;
  public string $status_approved;
  public string $status_rejected;

  /**
   * boot de datos
   * @return void
   */
  public function boot(PreOrderPeriodService $pps): void
  {
    // cada supplier tiene un usuario, y esta en sesion (Auth::user())
    $this->supplier = Auth::user()->supplier;

    $this->status_open = $pps->getStatusOpen();
    $this->status_closed = $pps->getStatusClosed();

    $this->status_pending = PreOrder::getPendingStatus();
    $this->status_approved = PreOrder::getApprovedStatus();
    $this->status_rejected = PreOrder::getRejectedStatus();
  }

  /**
   * reiniciar pagina para reestablecer la paginacion al
   * buscar periodos.
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar terminos de busqueda de los inputs
   * esto tambien reiniciara la paginacion.
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset(['search_word', 'period_status', 'preorder_status', 'period_start_at', 'period_end_at']);
    $this->resetPagination();
  }

  /**
   * buscar preordenes
   * @return mixed
   */
  public function searchPreorders()
  {
    // cada preorder tiene un supplier (proveedor) y un period
    // obtener la lista de preorders que tiene el proveedor en sesion
    $preorders = PreOrder::with(['supplier', 'pre_order_period'])
      ->where('supplier_id', $this->supplier->id)
      ->when($this->search_word, function ($query) {
        $query->where(function ($sub_query) {
          $sub_query->where('id', $this->search_word)
            ->orWhere('pre_order_code', 'like', '%' . $this->search_word . '%');
        });
      })
      ->when(isset($this->preorder_status) && $this->preorder_status !== '', function ($query) {
        $query->where('is_completed', $this->preorder_status);
      })
      ->when(isset($this->period_status) && $this->period_status !== '', function ($query) {
        $query->whereHas('pre_order_period', function ($sub_query) {
          $sub_query->where('period_status_id', $this->period_status);
        });
      })
      ->when($this->period_start_at && $this->period_end_at, function ($query) {
        $query->whereHas('pre_order_period', function ($sub_query) {
          $sub_query->where('period_start_at', '>=', $this->period_start_at)
            ->where('period_end_at', '<=', $this->period_end_at);
        });
      })
      ->when($this->period_start_at && !$this->period_end_at, function ($query) {
        $query->whereHas('pre_order_period', function ($sub_query) {
          $sub_query->where('period_start_at', '>=', $this->period_start_at);
        });
      })
      ->when(!$this->period_start_at && $this->period_end_at, function ($query) {
        $query->whereHas('pre_order_period', function ($sub_query) {
          $sub_query->where('period_end_at', '<=', $this->period_end_at);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $preorders;
  }

  /**
   * descargar orden de compra
   * @param int $id de pre orden base para el pdf
   */
  public function downloadPdfOrder(int $id)
  {
    // Generar URL para el PDF y notificacion email
    $pdfUrl = route('download-pdf-order', ['id' => $id]);
    // Disparar evento para abrir PDF en nueva pestaña
    $this->dispatch('downloadPdf', url: $pdfUrl);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $preorders = $this->searchPreorders();
    return view('livewire.quotations.list-pre-orders', compact('preorders'));
  }
}
