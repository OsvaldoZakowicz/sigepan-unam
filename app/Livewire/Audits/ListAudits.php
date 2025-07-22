<?php

namespace App\Livewire\Audits;

use App\Services\Audits\AuditService;
use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

class ListAudits extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  #[Url]
  public $event = '';

  #[Url]
  public $table = '';

  #[Url]
  public $search_start_at = '';

  #[Url]
  public $search_end_at = '';

  // lista de modelos auditados
  // modelo, tabla, atributos, con sus traducciones
  public $models = [];

  // lista de eventos;
  public $events = [];

  /**
   * montar datos
   * todo: traducir nombres de tablas usando lang
   * @return void
  */
  public function mount(): void
  {
    $audit_service = new AuditService();

    $this->events = $audit_service->getAuditEvents();
    $this->models = $audit_service->getAuditableModels();
  }

  /**
   * buscar auditorias, busca todas las auditorias paginadas
   * filtra auditorias cuando los parametros de filtrado existen
   * @return mixed
  */
  public function searchAudits()
  {
    return Audit::query()
      ->when($this->search, function ($query) {
        $query->where('id', $this->search);
      })
      ->when($this->event, function ($query) {
        $query->where('event', $this->event);
      })
      ->when($this->table, function ($query) {
        $query->where('auditable_type', $this->table);
      })
      ->when(
        $this->search_start_at || $this->search_end_at,
        function ($query) {
          // Manejo de intervalo de fechas
          if ($this->search_start_at && $this->search_end_at) {
            // Si tenemos ambas fechas, buscamos registros dentro del intervalo
            $query->whereBetween('created_at', [
              Carbon::parse($this->search_start_at)->startOfDay(),
              Carbon::parse($this->search_end_at)->endOfDay()
            ]);
          } elseif ($this->search_start_at) {
            // Si solo tenemos fecha inicial, buscamos desde esa fecha
            $query->where('created_at', '>=', Carbon::parse($this->search_start_at)->startOfDay());
          } elseif ($this->search_end_at) {
            // Si solo tenemos fecha final, buscamos hasta esa fecha
            $query->where('created_at', '<=', Carbon::parse($this->search_end_at)->endOfDay());
          }
        }
      )
      ->latest('id')
      ->paginate(10);
  }

  /**
   * reiniciar la paginacion al inicio
   * @return void
  */
  public function resetPagination()
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
    $this->reset(['search', 'event', 'table', 'search_start_at', 'search_end_at']);
    $this->resetPagination();
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $audits = $this->searchAudits();
    return view('livewire.audits.list-audits', compact('audits'));
  }
}
