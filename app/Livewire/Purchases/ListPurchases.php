<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use App\Services\Purchase\PurchaseService;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListPurchases extends Component
{
  use WithPagination;

  private PurchaseService $purchase_service;

  #[Url]
  public $search_purchase = '';

  // propiedades para el modal
  public bool $show_details_modal = false;
  public ?Purchase $selected_purchase = null;

  /**
   * boot de constantes
   * @return void
   */
  public function boot(): void
  {
    $this->purchase_service = new PurchaseService();
  }

  /**
   * abrir modal de detalle de compra
   * @param Purchase $purchase
   * @return void
   */
  public function openDetailsModal(Purchase $purchase): void
  {
    $this->selected_purchase = $purchase;
    $this->show_details_modal = true;
  }

  /**
   * cerrar modal de detalle de compra
   * @return void
   */
  public function closeDetailsModal(): void
  {
    $this->selected_purchase = null;
    $this->show_details_modal = false;
  }

  /**
   * obtener preorden de compra de referencia
   * para una compra, o retornar false
   * @param Purchase $purchase
   */
  public function preorderReference(Purchase $purchase)
  {
    return $this->purchase_service->getPreorderReference($purchase);
  }

  /**
   * buscar compras
   */
  public function searchPurchases()
  {
    return Purchase::paginate(10);
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
    $this->reset(['search_product', 'tag_filter', 'in_store_filter']);
  }

  public function render()
  {
    $purchases = $this->searchPurchases();
    return view('livewire.purchases.list-purchases', compact('purchases'));
  }
}
