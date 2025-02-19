<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\ProvisionTrademark;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListTrademarks extends Component
{
  use WithPagination;

  #[Url]
  public $search_input = '';

  /**
   * resetear la paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * borrar una marca
   * @param ProvisionTrademark $trademark
   * @return void
   */
  public function delete(ProvisionTrademark $trademark): void
  {
    if (!$trademark->provision_trademark_is_editable) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar la marca, la misma es propia del sistema',
      ]);

      return;
    }

    if ($trademark->provisions->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar la marca, la misma tiene suministros asociados',
      ]);

      return;
    }

    $trademark->delete();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'success',
      'title_toast' =>  toastTitle('exitosa'),
      'descr_toast' =>  toastSuccessBody('marca', 'eliminada'),
    ]);
  }

  /**
   * editar una marca
   * @param ProvisionTrademark $trademark
   * @return void
   */
  public function edit(ProvisionTrademark $trademark): void
  {
    if (!$trademark->provision_trademark_is_editable) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede editar la marca, la misma es propia del sistema',
      ]);

      return;
    }

    if ($trademark->provisions->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede editar la marca, la misma se usa en suministros',
      ]);

      return;
    }

    $this->redirectRoute('suppliers-trademarks-edit', $trademark->id, true, true);
  }

  /**
   * buscar marcas
   * @return mixed
   */
  public function searchTrademarks()
  {
    return ProvisionTrademark::when($this->search_input, function ($query) {
      $query->where('id', $this->search_input)
        ->orWhere('provision_trademark_name', 'like', '%' . $this->search_input . '%');
    })->orderBy('id', 'desc')->paginate(10);
  }

  /**
   * renderizar vista
   * @return view
   */
  public function render(): View
  {
    $trademarks = $this->searchTrademarks();
    return view('livewire.suppliers.list-trademarks', compact('trademarks'));
  }
}
