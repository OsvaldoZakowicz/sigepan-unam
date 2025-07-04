<?php

namespace App\Http\Controllers\Stocks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
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

  //* editar receta
  public function recipes_edit(int $id): View
  {
    return view('stocks.recipes-edit', ['id' => $id]);
  }

  //* ver receta
  public function recipes_show($id): View
  {
    return view('stocks.recipes-show', ['id' => $id]);
  }

  //* lista de productos
  public function products_index(): View
  {
    return view('stocks.products-index');
  }

  //* crear producto
  public function products_create(): View
  {
    return view('stocks.products-create');
  }

  //* ver un producto
  public function products_show(int $id): View
  {
    return view('stocks.products-show', ['id' => $id]);
  }

  //* ver stock del producto
  public function product_stock(int $id): View
  {
    return view('stocks.products-stock', ['id' => $id]);
  }

  //* editar un producto
  public function products_edit(int $id): View
  {
    return view('stocks.products-edit', ['id' => $id]);
  }

  //* ver existencias
  public function existences_index(): View
  {
    return view('stocks.existences-index');
  }

  //* lista de tags
  public function tags_index(): View
  {
    return view('stocks.tags-index');
  }

  //* crear tag
  public function tags_create(): View
  {
    return view('stocks.tags-create');
  }

  //* editar un tag
  public function tags_edit(int $id): View
  {
    return view('stocks.tags-edit', ['id' => $id]);
  }
}
