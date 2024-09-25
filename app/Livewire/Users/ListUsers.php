<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class ListUsers extends Component
{
  public $users;

  public function mount()
  {
    //* usuarios que no son clientes
    //! solucion temporal, evitar el hardcoding
    $this->users = User::withoutRole('cliente')->get();
  }

  public function render()
  {
    return view('livewire.users.list-users');
  }
}
