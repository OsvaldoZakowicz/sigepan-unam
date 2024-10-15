<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListUsers extends Component
{
  use WithPagination;

  //* eliminar un usuario
  public function delete(User $user)
  {
    // Un usuario no puede eliminarse a si mismo.
    if ($user->id === Auth::id()) {
      // todo: mensaje toast con el error de eliminacion de mi propia cuenta
      return;
    }

    //* CONTROL se quitan sus roles
    // para ello sincronizo con array vacio
    $user->syncRoles([]);

    //* CONTROL: se quitan sus permisos directos, si tuviere
    // para ello sincronizo con un array vacio
    $user->syncPermissions([]);

    $user->delete();
  }

  public function render()
  {
    // obtener usuarios que no son clientes, paginados
    $users = User::withoutRole('cliente')
      ->orderBy('id', 'desc')->paginate(10);

    return view('livewire.users.list-users', compact('users'));
  }
}
