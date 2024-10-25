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
  }

  //* eliminar un usuario
  public function delete(User $user)
  {
    // un usuario no puede eliminarse a si mismo
    if ($user->id === Auth::id()) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No puede eliminar su propia cuenta de usuario'
      ]);

      return;
    }

    $user_to_delete_role = $user->getRolenames()->first();

    // un usuario proveedor no puede eliminarse si tiene un proveedor asociado
    if ($user_to_delete_role === $this->RESTRICTED_ROLE) {

      if ($user->supplier->count() !== 0) {

        $this->dispatch('toast-event', toast_data: [
          'event_type'  => 'info',
          'title_toast' => toastTitle('', true),
          'descr_toast' => 'No puede eliminar el usuario proveedor: ' . $user->name . ', esta asociado a un proveedor existente'
        ]);

      }

      return;
    }

    // un usuario cliente no puede eliminarse
    // en caso de que por alguna razon se listen clientes
    if ($user_to_delete_role === $this->EXTERNAL_ROLE) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No puede eliminar el usuario cliente: ' . $user->name . ', no es un usuario gestionable'
      ]);

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
      'title_toast' => toastTitle(),
      'descr_toast' => toastSuccessBody('usuario', 'eliminado')
    ]);

    return;
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
