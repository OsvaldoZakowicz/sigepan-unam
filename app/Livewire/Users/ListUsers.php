<?php

namespace App\Livewire\Users;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\User\UserService;

class ListUsers extends Component
{
  use WithPagination;

  // variable necesaria para la busqueda
  protected $EXTERNAL_ROLE;

  #[Url]
  public $search = '';
  #[Url]
  public $role = '';

  // roles disponibles
  public $role_names;

  //* montar datos
  public function mount(UserService $user_service)
  {
    // recuperar roles para el filtrado en la busqueda
    $this->role_names = Role::whereNotIn('name', [$user_service->getExternalRole()])
      ->pluck('name');
  }

  //* eliminar un usuario
  public function delete(UserService $user_service, User $user)
  {
    // un usuario no puede eliminarse a si mismo
    if ($user_service->isUserOnSession($user)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No puede eliminar su propia cuenta de usuario'
      ]);

      return; // detener borrado
    }

    // un usuario proveedor no puede eliminarse si tiene un proveedor asociado
    if ($user_service->isSupplierUserWithSupplier($user)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No puede eliminar el usuario proveedor: ' . $user->name . ', esta asociado a un proveedor existente'
      ]);

      return; // detener borrado
    }

    // un usuario cliente no puede eliminarse
    // en caso de que por alguna razon se listen clientes
    if ($user_service->isClientUser($user)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No puede eliminar el usuario cliente: ' . $user->name . ', no es un usuario gestionable'
      ]);

      return; // detener borrado;
    }

    //* eliminar usuario
    try {

      // capturo el rol, en caso de que el borrado falle
      $user_role = $user->getRolenames()->first();

      $user_service->deleteInternalUser($user);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle(),
        'descr_toast' => toastSuccessBody('usuario', 'eliminado')
      ]);

    } catch (\Exception $e) {

      // devolver rol al usuario
      $user->assignRole($user_role);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'error',
        'title_toast' => toastTitle('fallida'),
        'descr_toast' => 'error: ' . $e->getMessage() . ' Contacte con el Administrador'
      ]);

    }
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

  public function render(UserService $user_service)
  {
    // rol externo, necesario por que condiciona la busqueda
    $this->EXTERNAL_ROLE = $user_service->getExternalRole();

    $users = $this->searchUsers();

    return view('livewire.users.list-users', compact('users'));
  }
}
