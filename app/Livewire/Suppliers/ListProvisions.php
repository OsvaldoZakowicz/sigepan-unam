<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use Livewire\Component;

class ListProvisions extends Component
{

  public function delete(Provision $provision)
  {
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

  public function render()
  {
    $provisions = Provision::all();

    return view('livewire.suppliers.list-provisions', compact('provisions'));
  }
}
