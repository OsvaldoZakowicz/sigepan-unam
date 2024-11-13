<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListProvisions extends Component
{
  use WithPagination;

  #[Url]
  public $search;
  #[Url]
  public $trademark_filter;
  #[Url]
  public $type_filter;

  public $trademarks;
  public $provision_types;

  //* montar datos
  public function mount()
  {
    $this->trademarks = ProvisionTrademark::orderBy('id', 'desc')->get();
    $this->provision_types = ProvisionType::all();
  }

  //* borrar suministro
  public function delete(Provision $provision)
  {
    // todo: controles previos al borrado, cuando es seguro borrar?
    //dd($provision);

    try {

      $provision->delete();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'success',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  toastSuccessBody('suministro', 'eliminado'),
      ]);

    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  'error: ' . $e->getMessage() . ' contacte al Administrador',
      ]);

    }

  }

  //* reiniciar la pagina para establecer la paginacion al inicio y buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  ///* buscar suministro
  public function searchProvision()
  {
    return Provision::when($this->search, function ($query) {
                        $query->where('id', 'like', '%' . $this->search . '%')
                              ->orWhere('provision_name', 'like', '%' . $this->search . '%');
                      })
                      ->when($this->trademark_filter, function ($query) {
                        $query->where('provision_trademark_id', $this->trademark_filter);
                      })
                      ->when($this->type_filter, function ($query) {
                        $query->where('provision_type_id', $this->type_filter);
                      })
                      ->orderBy('id', 'desc')->paginate(10);
  }

  public function render()
  {
    $provisions = $this->searchProvision();

    return view('livewire.suppliers.list-provisions', compact('provisions'));
  }
}
