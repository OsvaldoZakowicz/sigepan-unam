<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class ListUsers extends Component
{
  public $users;

  public function mount()
  {
    $this->users = User::all();
  }

  public function render()
  {
    return view('livewire.users.list-users');
  }
}
