<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use Livewire\Component;

class ListProvisions extends Component
{
  public function render()
  {
    $provisions = Provision::all();

    return view('livewire.suppliers.list-provisions', compact('provisions'));
  }
}
