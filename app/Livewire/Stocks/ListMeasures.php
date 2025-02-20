<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\Measure;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListMeasures extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  /**
   * resetear paginacion
   * @return void
  */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * buscar unidades de medida
   * @return mixed
  */
  public function searchMeasures()
  {
    return Measure::when($this->search, function ($query) {
        $query->where('unit_name', 'like', '%' . $this->search . '%');
      })
      ->orderBy('id', 'desc')
      ->paginate(5);
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $measures = $this->searchMeasures();
    return view('livewire.stocks.list-measures', compact('measures'));
  }
}
