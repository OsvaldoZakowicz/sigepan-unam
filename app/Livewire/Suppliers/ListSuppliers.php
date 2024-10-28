<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\Supplier\SupplierService;
use Livewire\Component;

class ListSuppliers extends Component
{
  //* eliminar proveedor
  public function delete(SupplierService $supplier_service, Supplier $supplier)
  {
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
        'descr_toast' => 'error: ' . $e->getMessage() . ', contacte al Administrador'
      ]);

    }
  }

  public function render()
  {
    $suppliers = Supplier::all();

    return view('livewire.suppliers.list-suppliers', compact('suppliers'));
  }
}
