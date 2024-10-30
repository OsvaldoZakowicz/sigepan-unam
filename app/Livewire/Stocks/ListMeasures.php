<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\Measure;

class ListMeasures extends Component
{
  public $measures;

  public function mount()
  {
    $this->measures = Measure::all();
  }

  public function render()
  {
    return view('livewire.stocks.list-measures');
  }
}
