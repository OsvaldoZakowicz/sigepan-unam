<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class ListPermissions extends Component
{
  use WithPagination;

  public function render()
  {
    $permissions = Permission::orderBy('id', 'desc')->paginate(10);

    return view('livewire.permissions.list-permissions', compact('permissions'));
  }
}
