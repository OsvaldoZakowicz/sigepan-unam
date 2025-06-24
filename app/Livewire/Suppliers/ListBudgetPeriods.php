<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RequestForQuotationPeriod;
use Illuminate\View\View;
use Livewire\Attributes\Url;

/**
 * * listar periodos de peticion de presupuestos.
 * La clase para el periodo de solicitud es: RequestForQuotationPeriod.
 * el componente ListBudgetPeriods lista los periodos de peticion de presupuestos
 */
class ListBudgetPeriods extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  #[Url]
  public $search_start_at = '';

  #[Url]
  public $search_end_at = '';

  /**
   * buscar periodos presupuestarios
   * search: id o codigo.
   * search_start_at: fecha de inicio.
   * search_end_at: fecha de fin.
   * @return mixed
   */
  public function searchPeriods()
  {
    return RequestForQuotationPeriod::when(
      $this->search,
      function ($query) {
        $query->where('id', 'like', '%' . $this->search . '%')
          ->orWhere('period_code', 'like', '%' . $this->search . '%');
      }
    )->when(
      $this->search_start_at && $this->search_end_at,
      function ($query) {
        // buscar periodos que esten completamente dentro del rango de fechas
        $query->where('period_start_at', '>=', $this->search_start_at)
          ->where('period_end_at', '<=', $this->search_end_at);
      }
    )->when(
      $this->search_start_at && !$this->search_end_at,
      function ($query) {
        // buscar periodos que coincidan con la fecha de inicio
        $query->where('period_start_at', '>=', $this->search_start_at);
      }
    )->when(
      !$this->search_start_at && $this->search_end_at,
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

  /**
   * eliminar priodo, solo si el estado es 'progrmado'
   * @param int $id del periodo
   * @return void 
   */
  public function deletePeriod(int $id): void
  {
    $period = RequestForQuotationPeriod::findOrFail($id);

    if ($period->status->status_code === 0) {

      try {

        $period->delete();

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'success',
          'title_toast' => toastTitle('exitosa'),
          'descr_toast' => 'periodo presupuestario eliminado'
        ]);
      } catch (\Exception $e) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'error',
          'title_toast' => toastTitle('fallida'),
          'descr_toast' => 'No se puede eliminar el periodo de presupuestos debido a ' .
            $e->getMessage() . ', contacte al administrador.',
        ]);
      }
    } else {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No es posible borrar el periodo.',
      ]);
    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $periods = $this->searchPeriods();
    return view('livewire.suppliers.list-budget-periods', compact('periods'));
  }
}
