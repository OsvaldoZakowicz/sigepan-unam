<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class ListAudits extends Component
{
  use WithPagination;

  public function render()
  {
    $audits = Audit::orderBy('id', 'desc')
      ->paginate(10);

    return view('livewire.audits.list-audits', compact('audits'));
  }
}
