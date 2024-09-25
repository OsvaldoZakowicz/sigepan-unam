<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class ListRoles extends Component
{
  public $roles;

  public function mount()
  {
    $this->roles = Role::all();
  }

  public function render()
  {
    return view('livewire.roles.list-roles');
  }
}
