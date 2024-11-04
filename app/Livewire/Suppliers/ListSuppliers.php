<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\Supplier\SupplierService;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListSuppliers extends Component
{
  use WithPagination;

  #[Url]
  public $search_input = '';

  public $iva_conditions = [];

  //* montar datos
  public function mount(SupplierService $supplier_service)
  {
    $this->iva_conditions = $supplier_service->getSuppilerIvaConditions();
  }

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

  //* buscar proveedores
  public function searchSuppliers()
  {
    return Supplier::when($this->search_input,
                      function ($query) {
                        $query->where('id', 'like', '%' . $this->search_input . '%')
                              ->orWhere('company_name', 'like', '%' . $this->search_input . '%')
                              ->orWhere('company_cuit', 'like', '%' . $this->search_input . '%')
                              ->orWhere('phone_number', 'like', '%' . $this->search_input . '%');
                      }
                    )->orderBy('id', 'desc')
                    ->paginate('10');
  }

  public function render()
  {
    $suppliers = $this->searchSuppliers();

    return view('livewire.suppliers.list-suppliers', compact('suppliers'));
  }
}
