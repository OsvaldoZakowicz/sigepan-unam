<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    //* lista de proveedores
    public function suppliers_index()
  {
    return view('suppliers.suppliers-index');
  }
}
