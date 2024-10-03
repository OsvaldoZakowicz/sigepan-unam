<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;

class ListAudits extends Component
{
  public $audits;

  public function mount()
  {
    $this->audits = Audit::all();
  }

  public function render()
  {
      return view('livewire.audits.list-audits');
  }
}
