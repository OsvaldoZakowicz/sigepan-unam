<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\ProvisionTrademark;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListTrademarks extends Component
{
  use WithPagination;

  #[Url]
  public $search_input = '';

  public function resetPagination()
  {
    $this->resetPage();
  }

  public function searchTrademarks()
  {
    return ProvisionTrademark::when($this->search_input, function ($query) {
                              $query->where('id', 'like', '%' . $this->search_input . '%')
                                    ->orWhere('provision_trademark_name', 'like', '%' . $this->search_input . '%');
                            })
                            ->orderBy('id', 'desc')->paginate(10);
  }

  public function render()
  {
    $trademarks = $this->searchTrademarks();

    return view('livewire.suppliers.list-trademarks', compact('trademarks'));
  }
}
