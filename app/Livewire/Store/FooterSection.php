<?php

namespace App\Livewire\Store;

use App\Models\DatoNegocio;
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
    return view('livewire.store.footer-section', compact('negocio'));
  }
}
