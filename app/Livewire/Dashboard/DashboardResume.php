<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\User;

/**
 * camponente que ancapsula a todos los
 * sub componentes del panel.
 */
class DashboardResume extends Component
{
  public function render()
  {
    return view('livewire.dashboard.dashboard-resume');
  }
}
