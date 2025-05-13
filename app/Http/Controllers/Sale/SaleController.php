<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
  //* mostrar todas las ventas
  public function sales_index():View
  {
    return view('sales.sales-index');
  }
}
