<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
  // * mostrar tienda publica
  public function store_index(): View
  {
    return view('store.store-index');
  }
}
