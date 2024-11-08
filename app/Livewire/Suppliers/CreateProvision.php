<?php

namespace App\Livewire\Suppliers;

use App\Models\Measure;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Livewire\Component;

class CreateProvision extends Component
{
  public $trademarks;
  public $measures;
  public $provision_types;

  public $provision_name;
  public $provision_quantity;
  public $provision_short_description = 'sin descripcion';
  public $provision_trademark_id;
  public $provision_type_id;
  public $measure_id;

  public function mount()
  {
    $this->trademarks = ProvisionTrademark::all();
    $this->measures = Measure::all();
    $this->provision_types = ProvisionType::all();
  }

  public function save()
  {
    dd([
      'name' => $this->provision_name,
      'quantity' => $this->provision_quantity,
      'description' => $this->provision_short_description,
      'trademark' => $this->provision_trademark_id,
      'type' => $this->provision_type_id,
      'measure' => $this->measure_id
    ]);
  }

  public function render()
  {
    return view('livewire.suppliers.create-provision');
  }
}
