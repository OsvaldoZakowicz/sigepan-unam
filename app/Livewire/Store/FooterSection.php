<?php

namespace App\Livewire\Store;

use App\Models\DatoNegocio;
use App\Models\DatoTienda;
use Illuminate\View\View;
use Livewire\Component;

class FooterSection extends Component
{
  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $negocio = DatoNegocio::obtenerTodos();
    $tienda = DatoTienda::obtenerTodos();
    return view('livewire.store.footer-section', compact('negocio', 'tienda'));
  }
}
