<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListAudits extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';
  #[Url]
  public $event = '';

  /**
   * * buscar auditorias
   * busca todas las auditorias paginadas
   * filtra auditorias cuando los parametros de filtrado existen
   * todo: filtrar por nombre de tabla
   * ?el nombre de la tabla necesita ser transformado
   */
  public function searchAudits()
  {
    return Audit::when($this->search, function ($query) {
            $query->where('id', 'like', '%'.$this->search.'%');
          })
          ->when($this->event, function ($query) {
            $query->where('event', 'like', $this->event);
          })
          ->orderBy('id', 'desc')
          ->paginate(10);
  }

  /**
   * * convertir nombre de tabla 'auditable_type' valido
   * en la tabla 'audits' el campo 'auditable_type' tiene la forma:
   * - Ruta\Del\Modelo\Modelo, por ejemplo: App\Models\User.
   * en el frontend, al mostrar el campo 'auditable_type' lo convierto de la siguiente forma:
   * - quito la ruta hasta obtener solo el modelo, por ejemplo: 'User'
   * - lo llevo al plural, por ejemplo: 'users'
   * - se traduce al español, por ejemplo: 'usuarios'
   * * necesito pasar de un termino de busqueda en español de un tabla a un
   * * termino de modelo en ingles y singular.
   */
  /* public function tableName()
  {
    $this->test = Str::singular($this->translate);
  } */

  /**
   * * reiniciar la paginacion al inicio
   * permite que al buscar se inicie siempre desde el principio
   * si busco desde la pagina 2, 3, ...n, retorna al principio y luego busca
   */
  public function resetPagination()
  {
    $this->resetPage();
  }

  public function render()
  {
    $audits = $this->searchAudits();

    return view('livewire.audits.list-audits', compact('audits'));
  }
}
