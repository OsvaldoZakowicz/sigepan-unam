<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EditUser extends Component
{
  protected $ROLES_INTERNOS = 'is_internal';
  protected $ROL_RESTRINGIDO = 'proveedor';

  // roles y usuario a editar
  public $roles;
  public $user;

  // datos a editar en el formulario
  public $user_name;
  public $user_email;
  public $user_role;

  public function mount($user_id)
  {
    // conseguir usuario
    $this->user = User::findOrFail($user_id);

    // un usuario no puede editarse a si mismo
    if ($this->user->id === Auth::id()) {
      //todo emitir un toast con mensaje de error
      $this->redirectRoute('users-users-index');
    }

    // recuperar roles
    $this->roles = Role::where($this->ROLES_INTERNOS, true)
      ->where('name', '!=', $this->ROL_RESTRINGIDO)
      ->get();

    // * completar propiedades a editar en el formulario a partir del usuario
    $this->user_name = $this->user->name;
    $this->user_email = $this->user->email;

    //? conseguir rol especifico del usuario
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
  public function update()
  {
    $this->validate([
      'user_name' => 'required|max:60',
      'user_email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->id)],
      'user_role' => 'required',
    ], [], [
      'user_name' => 'nombre de usuario',
      'user_email' => 'email',
      'user_role' => 'rol'
    ]);

    $this->user->name = $this->user_name;
    $this->user->email = $this->user_email;
    $this->user->save();
    // cambio de rol (siempre usar sync, ya que se reemplazan)
    $this->user->syncRoles($this->user_role);

    $this->reset(['user_name', 'user_email', 'user_role']);

    //todo: si cambio email, notificar usuario

    //todo: emitir un toast con mensaje de exito al editar con exito
    $this->redirectRoute('users-users-index');
  }

  public function render()
  {
    return view('livewire.users.edit-user');
  }
}
