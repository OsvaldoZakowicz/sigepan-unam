<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
   * SOFT DELETE: borrar suministro
   * solo cuando no este asociado a proveedores
   * @param Provision $provision
   * @return void
  */
  public function delete(Provision $provision): void
  {
    // si el suministro esta en listas de precios, no borrar
    if ($provision->suppliers->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar el suministro, está asociado a listas de precios de proveedores',
      ]);

      return;
    }

    // si el suministro tiene packs, no borrar
    if ($provision->packs->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar el suministro, tiene packs asociados',
      ]);

      return;
    }

    // si el suministro esta asociado a periodos de presupuesto, no borrar
    if ($provision->periods->count() > 0 || $provision->quotations->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar el suministro, tiene presupuestos y periodos presupuestarios asociados',
      ]);

      return;
    }

    // si el suministro esta asociado a periodos de preorden, no borrar
    if ($provision->pre_orders->count()) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar el suministro, tiene preordenes y periodos de preorden asociados',
      ]);

      return;
    }

    // si el suministro esta asociado a compras, no borrar
    if ($provision->purchase_details->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar el suministro, tiene compras asociadas',
      ]);

      return;
    }

    // si el suministro tiene existencias, no borrar
    if ($provision->existences->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar el suministro, tiene existencias asociadas',
      ]);

      return;
    }

    $provision->delete();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'info',
      'title_toast' =>  toastTitle('', true),
      'descr_toast' =>  'El suministro fue borrado con exito, aparcerá al final de la lista, puede restaurarlo de ser necesario',
    ]);

  }

  /**
   * restaurar un suministro borrado
   * @param int $id del suministro
   * @return void
  */
  public function restore(int $id): void
  {
    Provision::withTrashed()->where('id', $id)->restore();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'success',
      'title_toast' =>  toastTitle('exitosa'),
      'descr_toast' =>  'El suministro fue restaurado.'
    ]);
  }

  /**
   * editar suministro
   * solo cuando no este asociado a proveedores.
   * @param Provision $provision
   * @return void
  */
  public function edit(Provision $provision): void
  {
    // redirigir a la edicion
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
    return Provision::withTrashed()
      ->when($this->search, function ($query) {
        $query->where('id',$this->search)
              ->orWhere('provision_name', 'like', '%' . $this->search . '%');
      })
      ->when($this->trademark_filter, function ($query) {
        $query->where('provision_trademark_id', $this->trademark_filter);
      })
      ->when($this->type_filter, function ($query) {
        $query->where('provision_type_id', $this->type_filter);
      })
      ->orderBy('deleted_at', 'asc') // primero los NO borrados (deleted_at null)
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
