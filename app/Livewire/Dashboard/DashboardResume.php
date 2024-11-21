<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\User;

/**
 * todo: vista de resumen, estadisticas
 */
class DashboardResume extends Component
{
  public function render()
  {
    return view('livewire.dashboard.dashboard-resume');
  }
}
