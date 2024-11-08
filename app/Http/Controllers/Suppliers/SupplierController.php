<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
  //* lista de proveedores
  public function suppliers_index(): View
  {
    return view('suppliers.suppliers-index');
  }

  //* crear un proveedor
  public function suppliers_create(): View
  {
    return view('suppliers.suppliers-create');
  }

  //* ver un proveedor
  public function suppliers_show($id): View
  {
    return view('suppliers.suppliers-show', ['id' => $id]);
  }

  //* editar un proveedor
  public function suppliers_edit($id): View
  {
    return view('suppliers.suppliers-edit', ['id' => $id]);
  }

  //* ver suministros
  public function provisions_index(): View
  {
    return view('suppliers.provisions-index');
  }

  //* crear suministro
  public function provisions_create(): View
  {
    return view('suppliers.provisions-create');
  }
}
