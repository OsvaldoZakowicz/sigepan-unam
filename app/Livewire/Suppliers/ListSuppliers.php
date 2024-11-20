<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\Supplier\SupplierService;
use Illuminate\Database\QueryException;
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

      // todo: politicas para borrar un proveedor
      // //no permitir si tiene suministros asociados
      // no permitir si esta activo

      $supplier_service->deleteSupplier($supplier);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle(),
        'descr_toast' => toastSuccessBody('proveedor', 'eliminado')
      ]);

    } catch (QueryException $qe) {

      // capturar codigo del error
      $error_code = $qe->errorInfo[1];

      // error 1451 de clave foranea, restrict on delete
      if ($error_code == 1451) {

        $this->dispatch('toast-event', toast_data: [
          'event_type'  =>  'info',
          'title_toast' =>  toastTitle('', true),
          'descr_toast' =>  'No se puede eliminar el proveedor, tiene suministros asociados a su lista de precios',
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
