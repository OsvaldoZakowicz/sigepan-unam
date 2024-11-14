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

  //* lista de precios de un proveedor, id del proveedor
  // recupera para el proveedor su lista de suministros con precios
  public function suppliers_price_list($id): View
  {
    return view('suppliers.suppliers-prices-index', ['id' => $id]);
  }

  //* lista de alta de suministros con precio para un proveedor, id del proveedor
  public function suppliers_add_provision_price_to_provisions_price_list($id): View
  {
    return view('suppliers.suppliers-prices-create', ['id' => $id]);
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

  //* editar suministro
  public function provisions_edit($id): View
  {
    return view('suppliers.provisions-edit', ['id' => $id]);
  }

  //* ver marcas de suministros
  public function trademarks_index(): View
  {
    return view('suppliers.trademarks-index');
  }

  //* crear marca de suministros
  public function trademarks_create(): View
  {
    return view('suppliers.trademarks-create');
  }
}
