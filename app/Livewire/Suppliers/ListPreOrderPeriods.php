<?php

namespace App\Livewire\Suppliers;

use App\Models\PreOrderPeriod;
use App\Services\Supplier\PreOrderPeriodService;
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

  public $scheduled_status_id;

  private $preorder_period_service;

  /**
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    $this->preorder_period_service = new PreOrderPeriodService();
    $this->scheduled_status_id = $this->preorder_period_service->getStatusScheduled();
  }

  /**
   * buscar periodos de pre ordenes
   * search: id o codigo.
   * search_start_at: fecha de inicio.
   * search_end_at: fecha de fin.
   * @return mixed
   */
  public function searchPeriods()
  {
    return PreOrderPeriod::when(
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
   * borrar un periodo de pre ordenes
   * * unicamente si no ha abierto aun.
   * NOTA: los jobs programados fallarán.
   * @param int $id id del periodo a borrar.
   * @return void
   */
  public function delete(int $id): void
  {
    $period = PreOrderPeriod::findOrFail($id);

    if ($period->period_status_id === $this->scheduled_status_id) {

      $period->delete();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle('exitosa'),
        'descr_toast' => 'Periodo de pre ordenes eliminado correctamente.',
      ]);

      return;
    }

    $this->dispatch('toast-event', toast_data: [
      'event_type'  => 'info',
      'title_toast' => toastTitle('', true),
      'descr_toast' => 'No es posible eliminar el periodo de pre ordenes.',
    ]);
  }

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
