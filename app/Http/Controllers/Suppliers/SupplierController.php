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
}
