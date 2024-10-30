<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\User;

class DashboardResume extends Component
{
  public $suppliers_count;
  public $users_count;
  public $clients;

  public function mount()
  {
    $this->suppliers_count = Supplier::count();
  }

  public function render()
  {
    return view('livewire.dashboard.dashboard-resume');
  }
}
