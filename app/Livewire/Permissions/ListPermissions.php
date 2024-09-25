<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class ListPermissions extends Component
{
  public $permissions;

  public function mount()
  {
    $this->permissions = Permission::all();
  }

  public function render()
  {
    return view('livewire.permissions.list-permissions');
  }
}
