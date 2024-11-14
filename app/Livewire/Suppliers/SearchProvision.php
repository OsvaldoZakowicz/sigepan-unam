<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class SearchProvision extends Component
{
  use WithPagination;

  #[Url]
  public $search;

  //* enviar la provision elegida mediante un evento
  public function addProvision($id)
  {
    $this->dispatch('add-provision', id: $id);
  }

  public function render()
  {
    $provisions = Provision::when($this->search, function ($query) {
      $query->where('provision_name', 'like', '%' . $this->search . '%');
    })->orderBy('id', 'desc')->paginate(5);

    return view('livewire.suppliers.search-provision', compact('provisions'));
  }
}
