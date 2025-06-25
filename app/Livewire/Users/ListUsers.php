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
  protected $RESTRICTED_ROLE;

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
    $roles_filter = [$user_service->getExternalRole(), $user_service->getRestrictedRole()];
    $this->role_names = Role::whereNotIn('name', $roles_filter)->pluck('name');
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

    //* eliminar usuario
    try {

      $user_service->deleteInternalUser($user);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle(),
        'descr_toast' => toastSuccessBody('usuario', 'eliminado')
      ]);
    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'error',
        'title_toast' => toastTitle('fallida'),
        'descr_toast' => 'error: ' . $e->getMessage() . ' Contacte con el Administrador'
      ]);
    }
  }

  //* restaurar usuario
  public function restore(UserService $user_service, $id)
  {
    try {

      $user_service->restoreInternalUser($id);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'success',
        'title_toast' => toastTitle(),
        'descr_toast' => toastSuccessBody('usuario', 'restaurado')
      ]);
    } catch (\Exception $e) {

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
   * limpiar filtros
   * @return void
   */
  public function limpiar(): void
  {
    $this->reset(['search', 'role']);
  }

  /**
   * * buscar usuarios
   * busca todos los usuarios paginados
   * filtra usuarios cuando los parametros de filtrado existen
   */
  public function searchUsers()
  {
    return User::withTrashed()
      ->when($this->search, function ($query) {
        $query->where('id', 'like', '%' . $this->search . '%')
          ->orWhere('name', 'like', '%' . $this->search . '%')
          ->orWhere('email', 'like', '%' . $this->search . '%');
      })
      ->when($this->role, function ($query) {
        $query->role($this->role);
      }, function ($query) {
        $query->withoutRole([$this->EXTERNAL_ROLE, $this->RESTRICTED_ROLE]);
      })
      ->orderBy('deleted_at')
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  public function render(UserService $user_service)
  {
    // rol externo, necesario por que condiciona la busqueda
    $this->EXTERNAL_ROLE = $user_service->getExternalRole();
    $this->RESTRICTED_ROLE = $user_service->getRestrictedRole();

    $users = $this->searchUsers();

    return view('livewire.users.list-users', compact('users'));
  }
}
