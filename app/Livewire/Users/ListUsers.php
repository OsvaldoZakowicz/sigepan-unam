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

  protected $EXTERNAL_ROLE = 'cliente';
  protected $RESTRICTED_ROLE = 'proveedor';

  public $external_r;
  public $restricted_r;
  public $current_user;

  #[Url]
  public $search = '';
  #[Url]
  public $role = '';

  // roles disponibles
  public $role_names;

  //* montar datos
  public function mount()
  {
    // recuperar roles
    $this->role_names = Role::whereNotIn('name', [$this->EXTERNAL_ROLE])
      ->pluck('name');

    // rol externo
    $this->external_r = $this->getExternalRole();

    // rol restringido
    $this->restricted_r = $this->getRestrictedRole();

    // usuario en sesion
    $this->current_user = $this->getCurrentUser();

  }

  //* retornar nombre del rol restringido
  public function getRestrictedRole()
  {
    return $this->RESTRICTED_ROLE;
  }

  //* retornar nombre del rol externo
  public function getExternalRole()
  {
    return $this->EXTERNAL_ROLE;
  }

  //* retornar usuario en sesion
  public function getCurrentUser()
  {
    return Auth::user();
  }

  //* eliminar un usuario
  public function delete(User $user)
  {
    // un usuario no puede eliminarse a si mismo
    if ($user->id === $this->current_user->id) {
      // todo: mensaje toast con el error de eliminacion de mi propia cuenta
      return;
    }

    // un usuario proveedor o cliente no puede eliminarse
    $user_to_delete_role = $user->getRolenames()->first();
    if ($user_to_delete_role === $this->RESTRICTED_ROLE || $user_to_delete_role === $this->EXTERNAL_ROLE) {
      // todo: mensaje toast con el error de eliminacion de un proveedor o cliente
      return;
    }

    //* se quitan sus roles
    // para ello sincronizo con array vacio
    $user->syncRoles([]);

    //* se quitan sus permisos directos, si tuviere
    // para ello sincronizo con un array vacio
    $user->syncPermissions([]);

    $user->delete();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  => 'success',
      'title_toast' => 'Operación exitosa!',
      'descr_toast' => 'El usuario se eliminó correctamente'
    ]);
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
            $query->withoutRole($this->EXTERNAL_ROLE);
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
