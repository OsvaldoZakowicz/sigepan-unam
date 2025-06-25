<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\Supplier\SupplierService;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListSuppliers extends Component
{
  use WithPagination;

  #[Url]
  public $search_input = '';

  // condiciones frente al iva
  public $iva_conditions = [];

  /**
   * montar datos
   * @param SupplierService $supplier_service servicio para proveedores.
   * @return void.
   */
  public function mount(SupplierService $supplier_service): void
  {
    $this->iva_conditions = $supplier_service->getSuppilerIvaConditions();
  }

  /**
   * eliminar un proveedor
   * @param SupplierService $supplier_service servicio para proveedores.
   * @param Supplier $supplier proveedor
   * @return void
   */
  public function delete(SupplierService $supplier_service, Supplier $supplier): void
  {
    // Verificar si tiene suministros o packs asociados
    if ($supplier->provisions()->count() > 0 || $supplier->packs()->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede eliminar el proveedor porque tiene suministros o packs asociados'
      ]);

      return;
    }

    // verificar si esta sociado a compras
    if ($supplier->purchases()->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede eliminar el proveedor porque tiene compras asociadas'
      ]);

      return;
    }

    // verificar si esta sociado a periodos presupuestarios
    // nota periodos abiertos o historicos.
    if ($supplier->quotations()->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede eliminar el proveedor porque tiene presupuestos y periodos presupuestarios asociados'
      ]);

      return;
    }

    // verificar si esta sociado a periodos de preordenes
    // nota periodos abiertos o historicos.
    if ($supplier->pre_orders()->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede eliminar el proveedor porque tiene preordenes y periodos de preorden asociados'
      ]);

      return;
    }

    // eliminar proveedor
    try {
      $supplier_service->deleteSupplier($supplier);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle(),
        'descr_toast' => toastSuccessBody('proveedor', 'eliminado')
      ]);
    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'error',
        'title_toast' => toastTitle('fallida'),
        'descr_toast' => 'Error: ' . $e->getMessage() . ', contacte al Administrador.'
      ]);
    }
  }

  /**
   * restaurar un proveedor
   * @param SupplierService $supplier_service servicio para proveedores.
   * @param int $id supplier
   * @return void
   */
  public function restore(SupplierService $supplier_service, $id): void
  {
    try {
      $supplier_service->restoreSupplier($id);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle(),
        'descr_toast' => toastSuccessBody('proveedor', 'restaurado')
      ]);
    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'error',
        'title_toast' => toastTitle('fallida'),
        'descr_toast' => 'Error: ' . $e->getMessage() . ', contacte al Administrador.'
      ]);
    }
  }

  /**
   * buscar proveedores
   */
  public function searchSuppliers()
  {
    return Supplier::withTrashed()
      ->when(
        $this->search_input,
        function ($query) {
          $query->where('company_name', 'like', '%' . $this->search_input . '%')
            ->orWhere('company_cuit', 'like', '%' . $this->search_input . '%')
            ->orWhere('phone_number', 'like', '%' . $this->search_input . '%');
        }
      )
      ->orderBy('deleted_at')
      ->orderBy('id', 'desc')
      ->paginate('10');
  }

  /**
   * renderizar vista
   * @return view
   */
  public function render(): View
  {
    $suppliers = $this->searchSuppliers();
    return view('livewire.suppliers.list-suppliers', compact('suppliers'));
  }
}
