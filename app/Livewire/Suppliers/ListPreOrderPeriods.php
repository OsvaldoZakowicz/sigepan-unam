<?php

namespace App\Livewire\Suppliers;

use App\Models\PreOrderPeriod;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListPreOrderPeriods extends Component
{
  use WithPagination;


  #[Url]
  public $search = '';

  #[Url]
  public $search_start_at = '';

  #[Url]
  public $search_end_at = '';

  /**
   * buscar periodos de pre ordenes
   * search: id o codigo.
   * search_start_at: fecha de inicio.
   * search_end_at: fecha de fin.
   * @return mixed
  */
  public function searchPeriods()
  {
    return PreOrderPeriod::when($this->search,
      function ($query) {
        $query->where('id', 'like', '%' . $this->search . '%')
              ->orWhere('period_code', 'like', '%' . $this->search . '%');
      }
    )->when($this->search_start_at && $this->search_end_at,
      function ($query) {
        // buscar periodos que esten completamente dentro del rango de fechas
        $query->where('period_start_at', '>=', $this->search_start_at)
              ->where('period_end_at', '<=', $this->search_end_at);
      }
    )->when($this->search_start_at && !$this->search_end_at,
      function ($query) {
        // buscar periodos que coincidan con la fecha de inicio
        $query->where('period_start_at', '>=', $this->search_start_at);
      }
    )->when(!$this->search_start_at && $this->search_end_at,
      function ($query) {
        // buscar periodos que coincidan con la fecha de fin
        $query->where('period_end_at', '<=', $this->search_end_at);
      }
    )
    ->orderBy('id', 'desc')
    ->paginate(10);
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
    $this->reset(['search', 'search_start_at', 'search_end_at']);
    $this->resetPagination();
  }

  // TODO: Permitir el borrado de un periodo si no esta abierto.

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $preorder_periods = $this->searchPeriods();
    return view('livewire.suppliers.list-pre-order-periods', compact('preorder_periods'));
  }
}
