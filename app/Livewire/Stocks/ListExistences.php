<?php

namespace App\Livewire\Stocks;

use App\Models\Existence;
use App\Models\Provision;
use App\Models\ProvisionCategory;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListExistences extends Component
{

  use WithPagination;

  #[Url]
  public $search_category = '';

  public $show_details_modal = false;
  public $selected_category = null;

  /**
   * obtiene detalles de existencias por categoria
   * @param ProvisionCategory $category
   * @return void
   */
  public function showDetails(ProvisionCategory $category): void
  {
    $this->selected_category = $category;
    $this->show_details_modal = true;
  }

  /**
   * obtiene los suministros con existencias de una categoría
   * @return \Illuminate\Database\Eloquent\Collection
   */
  private function getProvisionDetails()
  {
    if (!$this->selected_category) {
      return collect();
    }

    return Provision::query()
      ->select(
        'provisions.id',
        'provisions.provision_trademark_id',
        'provisions.provision_category_id',
        'existences.movement_type',
        'existences.registered_at',
        'existences.quantity_amount'
      )
      ->with(['trademark:id,provision_trademark_name']) // eager loading de la marca
      ->leftJoin('existences', 'provisions.id', '=', 'existences.provision_id')
      ->where('provisions.provision_category_id', $this->selected_category->id)
      ->where('existences.quantity_amount', '!=', 0) // solo movimientos que afecten existencias
      ->orderBy('existences.registered_at', 'desc') // ordenar por fecha más reciente
      ->get();
  }

  /**
   * cierra el modal de detalles
   * @return void
   */
  public function closeDetails(): void
  {
    $this->show_details_modal = false;
    $this->selected_category = null;
  }

  /**
   * buscar existencias por categoria
   * @return \Illuminate\Database\Eloquent\Collection
   */
  private function searchExistences()
  {
    return ProvisionCategory::query()
      ->select('provision_categories.*')  // Seleccionamos todos los campos de la categoría
      ->selectRaw('COALESCE(SUM(existences.quantity_amount), 0) as total_quantity')
      ->with('measure')  // Eager loading de la relación measure
      ->leftJoin('provisions', 'provision_categories.id', '=', 'provisions.provision_category_id')
      ->leftJoin('existences', 'provisions.id', '=', 'existences.provision_id')
      ->when($this->search_category, function ($query) {
        return $query->where('provision_categories.provision_category_name', 'like', '%' . $this->search_category . '%')
          ->orWhere('provision_categories.id', 'like', '%' . $this->search_category . '%');
      })
      ->groupBy('provision_categories.id', 'provision_categories.provision_category_name', 'provision_categories.measure_id')
      ->orderByRaw('COALESCE(SUM(existences.quantity_amount), 0) DESC')
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
    $this->reset(['search_category']);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $provision_categories = $this->searchExistences();
    $provision_details = $this->getProvisionDetails();

    return view('livewire.stocks.list-existences', compact('provision_categories', 'provision_details'));
  }
}
