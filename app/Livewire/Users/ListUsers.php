<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListUsers extends Component
{
  public $users;

  //* obtener usuarios
  // usuarios que no son clientes
  //! solucion temporal, evitar el hardcoding
  public function getUsers()
  {
    $this->users = User::withoutRole('cliente')->get();
  }

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

    // refrescar los usuarios listados
    $this->getUsers();
  }

  public function mount()
  {
    $this->getUsers();
  }

  public function render()
  {
    return view('livewire.users.list-users');
  }
}
