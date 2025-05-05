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

  #[Url]
  public $search_start_at = '';

  #[Url]
  public $search_end_at = '';

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
    return Purchase::with(['purchase_details', 'supplier'])
      ->when($this->search_purchase, function ($query) {
        $query->where('id', 'like', '%' . $this->search_purchase . '%')
          ->orWhereHas('supplier', function ($query) {
            $query->where('company_name', 'like', '%' . $this->search_purchase . '%');
          });
      })->when(
        $this->search_start_at && $this->search_end_at,
        function ($query) {
          // buscar periodos que esten completamente dentro del rango de fechas
          $query->where('purchase_date', '>=', $this->search_start_at)
            ->where('purchase_date', '<=', $this->search_end_at);
        }
      )->when(
        $this->search_start_at && !$this->search_end_at,
        function ($query) {
          // buscar periodos que coincidan con la fecha de inicio
          $query->where('purchase_date', '>=', $this->search_start_at);
        }
      )->when(
        !$this->search_start_at && $this->search_end_at,
        function ($query) {
          // buscar periodos que coincidan con la fecha de fin
          $query->where('purchase_date', '<=', $this->search_end_at);
        }
      )
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
    $this->reset(['search_purchase', 'search_start_at', 'search_end_at']);
  }

  public function render()
  {
    $purchases = $this->searchPurchases();
    return view('livewire.purchases.list-purchases', compact('purchases'));
  }
}
