<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;

class ShowAudits extends Component
{
  public $audit;

  public function mount($id)
  {
    $this->audit = Audit::findOrFail($id);
  }

  public function render()
  {
      return view('livewire.audits.show-audits');
  }
}
