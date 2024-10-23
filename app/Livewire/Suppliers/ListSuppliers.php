<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;

class ListSuppliers extends Component
{
  /**
   * * eliminar un proveedor
   * si tiene asociado un usuario y direccion, eliminar
   */
  public function delete(Supplier $supplier)
  {
    $supplier_user = $supplier->user;
    $supplier_address = $supplier->address;

    $supplier->delete();

    if ($supplier_user) {
      $supplier_user->delete();
    }

    if ($supplier_address) {
      $supplier_address->delete();
    }
  }

  public function render()
  {
    $suppliers = Supplier::all();

    return view('livewire.suppliers.list-suppliers', compact('suppliers'));
  }
}
