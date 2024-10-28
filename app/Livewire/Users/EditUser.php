<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\User\UserService;

class EditUser extends Component
{
  // roles y usuario a editar
  public $roles;
  public $user;

  // datos a editar en el formulario
  public $user_name;
  public $user_email;
  public $user_role;

  //* montar datos
  public function mount(UserService $user_service, $user_id)
  {
    // conseguir usuario
    $this->user = User::findOrFail($user_id);

    // un usuario no puede editarse a si mismo
    if ($user_service->isUserOnSession($this->user)) {

      session()->flash('operation-info', 'No puede editar su propia cuenta de usuario');
      $this->redirectRoute('users-users-index');
    }

    // un usuario proveedor no puede editarse
    if ($user_service->isSupplierUserWithSupplier($this->user)) {

      session()->flash('operation-info', 'No puede editar un usuario proveedor, debe gestionarlo a traves de la seccion "proveedores"');
      $this->redirectRoute('users-users-index');
    }

    // un usuario cliente no puede editarse
    // en caso de que por alguna razon se listen clientes
    if ($user_service->isClientUser($this->user)) {

      session()->flash('operation-info', 'No puede editar un usuario cliente, no es gestionable');
      $this->redirectRoute('users-users-index');
    }

    // recuperar roles
    $this->roles = Role::where($user_service->getInternalRoleAttribute(), true)
      ->where('name', '!=', $user_service->getRestrictedRole())
      ->get();

    // * completar propiedades a editar en el formulario a partir del usuario
    $this->user_name = $this->user->name;
    $this->user_email = $this->user->email;

    //* conseguir rol especifico del usuario
    // busco por el nombre, que es unico
    // considerar que un usuario tiene un rol nada mas, o ninguno
    if ($this->user->hasAnyRole($this->roles->pluck('name')->toArray())) {
      $user_exact_role = Role::where('name', $this->user->getRoleNames())->first();
      $this->user_role = $user_exact_role->name;
    }
  }

  /**
   * * actualizar el usuario
   * * asignarle las propiedades del formulario al usuario
   */
  public function update(UserService $user_service)
  {
    $validated = $this->validate([
      'user_name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/u',
      'user_email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->id)],
      'user_role' => 'required',
    ], [
      'user_name.regex' => 'El :attribute debe contener letras y espacios solamente',
    ], [
      'user_name' => 'nombre de usuario',
      'user_email' => 'email',
      'user_role' => 'rol'
    ]);

    try {

      $user_service->editInternalUser($this->user, $validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('usuario', 'editado'));

      $this->redirectRoute('users-users-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');

      $this->redirectRoute('users-users-index');

    }

  }

  public function render()
  {
    return view('livewire.users.edit-user');
  }
}
