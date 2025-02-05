<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * * controlador de presupuestos de proveedores
 * * mantendra los presupuestos de los proveedores.
 */
class QuotationController extends Controller
{
  //* lista de presupuestos solicitados al proveedor
  public function quotations_index(): View
  {
    return view('quotations.quotations-index');
  }

  //* responder a una solicitud de presupuesto
  public function quotations_respond($id): View
  {
    return view('quotations.quotations-respond', ['id' => $id]);
  }

  //* editar respuesta de una solicitud de presupuesto
  public function quotations_edit($id): View
  {
    return view('quotations.quotations-edit', ['id' => $id]);
  }
}
