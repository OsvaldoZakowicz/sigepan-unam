<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CreateUser extends Component
{
  protected $ROLES_INTERNOS = 'is_internal';
  protected $ROL_RESTRINGIDO = 'proveedor';

  public $roles;
  public $user_name;
  public $user_email;
  public $user_password;
  public $user_password_test;
  public $user_role;

  //* montar datos
  public function mount()
  {
    // recuperar roles
    $this->roles = Role::where($this->ROLES_INTERNOS, true)
      ->where('name', '!=', $this->ROL_RESTRINGIDO)
      ->get();
  }

  //* crear usuario
  public function save()
  {
    $this->validate([
      'user_name' => 'required|max:60',
      'user_email' => 'required|email|unique:users,email',
      'user_password' => 'required|min:8',
      'user_password_test' => 'required|min:8|same:user_password',
      'user_role' => 'required',
    ], [], [
      'user_name' => 'nombre de usuario',
      'user_email' => 'email',
      'user_password' => 'contraseña',
      'user_password_test' => 'repetir contraseña',
      'user_role' => 'rol'
    ]);

    //todo: try-catch?
    // - asegurar que se cree el usuario y asigne el rol, o fallar
    $new_user = User::create([
      'name' => $this->user_name,
      'email' => $this->user_email,
      'password' => bcrypt($this->user_password),
    ]);

    $new_user->assignRole($this->user_role);

    //todo: si la creacion es correcta:
    // - enviar acceso via email al nuevo usuario

    $this->reset(['user_name', 'user_email', 'user_password', 'user_password_test', 'user_role']);

    //todo: emitir un toast con mensaje de exito al retornar de exito
    $this->redirectRoute('users-users-index');
  }

  public function render()
  {
    return view('livewire.users.create-user');
  }
}
