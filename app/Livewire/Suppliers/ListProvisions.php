<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
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

  /**
   * montar datos
   * @return void
  */
  public function mount(): void
  {
    $this->trademarks = ProvisionTrademark::orderBy('id', 'desc')->get();
    $this->provision_types = ProvisionType::all();
  }

  /**
   * borrar suministro
   * solo cuando no este asociado a proveedores
   * @param Provision $provision
   * @return void
  */
  public function delete(Provision $provision): void
  {
    // todo: cuando el suministro se use en recetas, no permitir el borrado

    try {

      $provision->delete();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'success',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  toastSuccessBody('suministro', 'eliminado'),
      ]);

    } catch (QueryException $qe) {

      // capturar codigo del error
      $error_code = $qe->errorInfo[1];

      // error 1451 de clave foranea, restrict on delete
      if ($error_code == 1451) {

        $this->dispatch('toast-event', toast_data: [
          'event_type'  =>  'info',
          'title_toast' =>  toastTitle('', true),
          'descr_toast' =>  'No se puede eliminar el suministro, está asociado a listas de precios de proveedores',
        ]);

        return;

      }

      // otro error
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  'Error inesperado: ' . $qe->getMessage() . 'contacte al Administrador',
      ]);

    }

  }

  /**
   * editar suministro
   * solo cuando no este asociado a proveedores.
   * @param Provision $provision
   * @return void
  */
  public function edit(Provision $provision): void
  {
    if ($provision->suppliers->count() !== 0) {

      // no editar, esta asociado a proveedores
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'El suministro no se puede editar, está asociado a listas de precios de proveedores',
      ]);

      return;

    }

    // rdirigir a la edicion
    $this->redirectRoute('suppliers-provisions-edit', $provision->id, true, true);
  }

  /**
   * resetear la paginacion
   * @return void
  */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * resetear los filtros de busqueda
   * @return void
  */
  public function resetSearchInputs(): void
  {
    $this->reset('search', 'trademark_filter', 'type_filter');
  }

  /**
   * buscar suministros
   * @return mixed
  */
  public function searchProvision()
  {
    return Provision::when($this->search, function ($query) {
        $query->where('id',$this->search)
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

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $provisions = $this->searchProvision();
    return view('livewire.suppliers.list-provisions', compact('provisions'));
  }
}
