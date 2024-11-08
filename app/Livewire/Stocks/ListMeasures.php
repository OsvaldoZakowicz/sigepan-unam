<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\Measure;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListMeasures extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  public function toast()
  {
    $this->dispatch('toast-event', toast_data: [
      'event_type'  => 'info',
      'title_toast' => toastTitle('', true),
      'descr_toast' => 'Esta unidad de medida no puede editarse o borrarse, es predefinida del sistema.'
    ]);
  }

  public function searchMeasures()
  {
    return Measure::when($this->search, function ($query) {
      $query->where('measure_name', 'like', '%'. $this->search .'%')
            ->orWhere('measure_abrv', 'like', '%'. $this->search .'%')
            ->orWhere('measure_base', 'like', $this->search);
    })
    ->orderBy('id', 'desc')
    ->paginate(10);
  }

  public function render()
  {
    $measures = $this->searchMeasures();

    return view('livewire.stocks.list-measures', compact('measures'));
  }
}
