<?php

namespace App\Livewire\Purchases;

use App\Models\PreOrder;
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

  #[Url]
  public $search_preorder = '';

  // posibles estados de preorden
  public string $status_pending;
  public string $status_approved;
  public string $status_rejected;

  /**
   * boot de datos constantes
   */
  public function boot(): void
  {
    // posibles estados de preorden
    $this->status_pending = PreOrder::getPendingStatus();
    $this->status_approved = PreOrder::getApprovedStatus();
    $this->status_rejected = PreOrder::getRejectedStatus();
  }

  /**
   * buscar compras
   */
  public function searchPurchases()
  {
    return PreOrder::paginate(10);
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
    $this->reset(['search_preorder']);
  }

  public function render()
  {
    $preorders = $this->searchPurchases();
    return view('livewire.purchases.list-pre-orders', compact('preorders'));
  }
}
