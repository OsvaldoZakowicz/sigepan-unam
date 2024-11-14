<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Provision;
use App\Models\Supplier;
use Livewire\Attributes\On;

//* componente de formulario individual para actualizar un precio
class InputPriceForm extends Component
{
  public $supplier;
  public $provision;

  // campos del formulario
  public $provision_price;

  public function mount($provision_id, $supplier_id)
  {
    $this->provision = Provision::findOrFail($provision_id);
    $this->supplier = Supplier::findOrFail($supplier_id);
  }

  //* al recibir notificacion de guardado
  #[On('save-prices')]
  public function savePrice()
  {
    dd($this->provision_price);
  }

  public function render()
  {
    return view('livewire.suppliers.input-price-form');
  }
}
