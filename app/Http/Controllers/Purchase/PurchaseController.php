<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
  //* listar compras
  public function purchases_index(): View
  {
    return view('purchases.purchases-index');
  }

  //* crear nueva compra
  public function purchases_create(?int $id = null):View
  {
    return view('purchases.purchases-create', ['id' => $id]);
  }

  //* listar pre ordenes
  public function purchases_preorders_index(): View
  {
    return view('purchases.purchases-preorders-index');
  }
}
