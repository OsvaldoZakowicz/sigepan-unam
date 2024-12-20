<?php

namespace App\Http\Controllers\Stocks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
  //* lista de stock de productos
  public function stocks_index(): View
  {
    return view('stocks.stocks-index');
  }

  //* lista de unidades de medida
  public function measures_index(): View
  {
    return view('stocks.measures-index');
  }

  //* crear unidad de medida
  public function measures_create(): View
  {
    return view('stocks.measures-create');
  }

  //* lista de recetas
  public function recipes_index(): View
  {
    return view('stocks.recipes-index');
  }

  //* crear receta
  public function recipes_create(): View
  {
    return view('stocks.recipes-create');
  }
}
