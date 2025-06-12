<?php

namespace App\Livewire\Purchases;

use App\Models\PreOrder;
use App\Services\Supplier\PreOrderService;
use App\Models\Purchase;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * *MODULO DE COMPRAS
 * LISTAR TODAS LAS PRE ORDENES
 */
class ListPreOrders extends Component
{
  use WithPagination;

  private PreOrderService $preorder_service;

  #[Url]
  public $search_preorder = '';

  #[Url]
  public $search_start_at = '';

  #[Url]
  public $search_end_at = '';

  #[Url]
  public $status_filter = '';

  // filtrado sin usar url
  public $status_purchase_filter = '';

  // posibles estados de preorden
  public string $status_pending;
  public string $status_approved;
  public string $status_rejected;

  /**
   * boot de datos constantes
   */
  public function boot(): void
  {
    $this->preorder_service = new PreOrderService();

    // posibles estados de preorden
    $this->status_pending = PreOrder::getPendingStatus();
    $this->status_approved = PreOrder::getApprovedStatus();
    $this->status_rejected = PreOrder::getRejectedStatus();
  }

  /**
   * comprobar si la preorden tiene una compra asociada
   * @param PreOrder $preorder
   * @return bool
   */
  public function checkAsociatedPurchase(PreOrder $preorder): bool
  {
    return $this->preorder_service->hasAssociatedPurchase($preorder);
  }

  /**
   * ir a la vista de compra realizada
   * @param PreOrder $preorder
   */
  public function goToPurchase(PreOrder $preorder)
  {
    $purchase = Purchase::where('purchase_reference_id', $preorder->id)
      ->where('purchase_reference_type', get_class($preorder))
      ->first();

    if ($purchase) {
      // almacenar el ID en la sesion para mantenerlo despues del redirect
      session()->flash('pending_purchase_id', $purchase->id);
      return redirect()->route('purchases-purchases-index');
    }
  }

  /**
   * ir a la vista de preorden
   * @param PreOrder $preorder
   */
  public function goToPreorder(PreOrder $preorder)
  {
    return redirect()->route('suppliers-preorders-response', ['id' => $preorder->id]);
  }

  /**
   * buscar preordenes
   */
  public function searchPreOrders()
  {
    return PreOrder::with(['supplier'])
      ->when($this->search_preorder, function ($query) {
        $query->where('id', 'like', '%' . $this->search_preorder . '%')
          ->orWhere('pre_order_code', 'like', '%' . $this->search_preorder . '%')
          ->orWhereHas('supplier', function ($query) {
            $query->where('company_name', 'like', '%' . $this->search_preorder . '%');
          });
      })->when(
        $this->search_start_at && $this->search_end_at,
        function ($query) {
          // buscar preordenes que esten completamente dentro del rango de fechas
          $query->where('created_at', '>=', $this->search_start_at)
            ->where('created_at', '<=', $this->search_end_at);
        }
      )->when(
        $this->search_start_at && !$this->search_end_at,
        function ($query) {
          // buscar preordenes que coincidan con la fecha de inicio
          $query->where('created_at', '>=', $this->search_start_at);
        }
      )->when(
        !$this->search_start_at && $this->search_end_at,
        function ($query) {
          // buscar preordenes que coincidan con la fecha de fin
          $query->where('created_at', '<=', $this->search_end_at);
        }
      )->when($this->status_filter, function ($query) {
        $query->where('status', $this->status_filter);
      })
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  /**
   * resetear la paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar campos de busqueda
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset([
      'search_preorder', 'search_start_at', 'search_end_at', 'status_filter', 'status_purchase_filter'
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $preorders = $this->searchPreOrders();
    return view('livewire.purchases.list-pre-orders', compact('preorders'));
  }
}
