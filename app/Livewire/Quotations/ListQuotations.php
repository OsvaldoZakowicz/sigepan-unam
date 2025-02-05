<?php

namespace App\Livewire\Quotations;

use App\Models\Quotation;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListQuotations extends Component
{
  use WithPagination;

  #[Url]
  public $search_word = '';

  #[Url]
  public $quotation_status = '';

  #[Url]
  public $period_status = '';

  #[Url]
  public $period_start_at = '';

  #[Url]
  public $period_end_at = '';

  public Supplier $supplier;

  /**
   * boot de datos
   * @return void
  */
  public function boot(): void
  {
    // cada supplier tiene un usuario, y esta en sesion (Auth::user())
    $this->supplier = Auth::user()->supplier;
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
    $this->reset(['search_word', 'period_status', 'quotation_status', 'period_start_at', 'period_end_at']);
    $this->resetPagination();
  }

  /**
   * buscar presupuestos
   * @return mixed
  */
  public function searchQuotations()
  {
    // cada quotation tiene un supplier (proveedor) y un period
    // obtener la lista de quotations que tiene el proveedor en sesion
    $quotations = Quotation::with(['supplier', 'period'])
      ->where('supplier_id', $this->supplier->id)
      ->when($this->search_word, function ($query) {
        $query->where('id', $this->search_word)
            ->orWhere('quotation_code', 'like', '%' . $this->search_word . '%');
      })
      ->when(isset($this->quotation_status) && $this->quotation_status !== '', function ($query) {
        $query->where('is_completed', $this->quotation_status);
      })
      ->when(isset($this->period_status) && $this->period_status !== '', function ($query) {
        $query->whereHas('period', function($q) {
          $q->where('period_status_id', $this->period_status);
        });
      })
      ->when($this->period_start_at && $this->period_end_at, function ($query) {
        $query->whereHas('period', function($q) {
          $q->where('period_start_at', '>=', $this->period_start_at)
            ->where('period_end_at', '<=', $this->period_end_at);
        });
      })
      ->when($this->period_start_at && !$this->period_end_at, function ($query) {
        $query->whereHas('period', function($q) {
          $q->where('period_start_at', '>=', $this->period_start_at);
        });
      })
      ->when(!$this->period_start_at && $this->period_end_at, function ($query) {
        $query->whereHas('period', function($q) {
          $q->where('period_end_at', '<=', $this->period_end_at);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $quotations;
  }

  /**
   * renderizar vista
   * @return view
  */
  public function render(): View
  {
    $quotations = $this->searchQuotations();
    return view('livewire.quotations.list-quotations', compact('quotations'));
  }
}
