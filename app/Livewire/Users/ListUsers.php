<?php

namespace App\Livewire\Users;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListUsers extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';
  #[Url]
  public $role = '';

  // roles disponibles
  public $role_names;

  //* montar datos necesarios para iniciar
  public function mount()
  {
    $this->role_names = Role::whereNotIn('name', ['cliente'])->pluck('name');
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
  }

  /**
   * * reiniciar la paginacion al inicio
   * permite que al buscar se inicie siempre desde el principio
   * si busco desde la pagina 2, 3, ...n, retorna al principio y luego busca
   */
  public function resetPagination()
  {
    $this->resetPage();
  }

  /**
   * * buscar usuarios
   * busca todos los usuarios paginados
   * filtra usuarios cuando los parametros de filtrado existen
   */
  public function searchUsers()
  {
    return User::when($this->search, function ($query) {
            $query->where('id', 'like', '%'.$this->search.'%')
                  ->orWhere('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
          })
          ->when($this->role, function ($query) {
            $query->role($this->role);
          }, function ($query) {
            $query->withoutRole('cliente');
          })
          ->orderBy('id', 'desc')
          ->paginate(10);
  }

  public function render()
  {
    $users = $this->searchUsers();

    return view('livewire.users.list-users', compact('users'));
  }
}
