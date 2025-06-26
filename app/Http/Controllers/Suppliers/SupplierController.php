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

  //* lista de edicion de suministros con precio para un proveedor, id del proveedor
  public function suppliers_edit_provision_price_to_provisions_price_list($id): View
  {
    return view('suppliers.suppliers-prices-edit', ['id' => $id]);
  }

  //* lista de precios generales de todos los proveedores
  public function suppliers_all_prices_list(): View
  {
    return view('suppliers.suppliers-all-prices-index');
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

  //* ver categorias de suministros
  public function categories_index(): View
  {
    return view('suppliers.categories-index');
  }

  //* crear categoria de suministros
  public function categories_create(): View
  {
    return view('suppliers.categories-create');
  }

  //* editar categoria de suministros
  public function categories_edit($id): View
  {
    return view('suppliers.categories-edit', ['id' => $id]);
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

  //* editar marca de suministros
  public function trademarks_edit($id): View
  {
    return view('suppliers.trademarks-edit', ['id' => $id]);
  }

  //* listar periodos de peticion de presupuestos
  public function budget_periods_index(): View
  {
    return view('suppliers.budgets-periods-index');
  }

  //* crear periodo de peticion de presupuestos
  public function budget_periods_create(): View
  {
    return view('suppliers.budgets-periods-create');
  }

  //* editar y reabrir periodo presupuestario
  public function budget_periods_edit($id): View
  {
    return view('suppliers.budget-periods-edit', ['id' => $id]);
  }

  //* ver periodo de peticion de presupuestos
  public function budget_periods_show($id): View
  {
    return view('suppliers.budgets-periods-show', ['id' => $id]);
  }

  //* ver respuestas de un presupuesto
  public function budget_response($id): View
  {
    return view('suppliers.budget-response', ['id' => $id]);
  }

  //* ver ranking de presupuestos de un periodo
  public function budget_ranking($id): View
  {
    return view('suppliers.budget-ranking', ['id' => $id]);
  }

  //* listar periodo de preordenes de compra
  public function preorder_index(): View
  {
    return view('suppliers.preorders-index');
  }

  //* crear periodo de preorden de compra
  public function preorder_create($id = null): View
  {
    return view('suppliers.preorders-create', ['id' => $id]);
  }

  //* editar periodo de preorden de compra
  public function preorder_edit($id): View
  {
    return view('suppliers.preorders-edit', ['id' => $id]);
  }

  //* ver periodo de preorden de compra
  public function preorder_show($id): View
  {
    return view('suppliers.preorders-show', ['id' => $id]);
  }

  //* ver respuesta de una pre orden de compra
  public function preorder_response($id): View
  {
    return view('suppliers.preorder-response', ['id' => $id]);
  }
}
