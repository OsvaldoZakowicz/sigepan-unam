<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Rule; // validaciones de livewire

/**
 * * clase CrearRol
 * actua como parte de un controlador
 */
class CreateRole extends Component
{
  //* propiedades accesibles
  // del modelo:
  public $permissions;
  public $roles;

  //* propiedades accesibles de la vista
  // de la vista:
  //?validacion desde reglas de livewire!:
  #[Rule('required')]
  public $role_name;
  #[Rule('required', message: 'Este campo :attribute falta', as: 'descripcion corta')]
  public $role_short_description;
  #[Rule('required')]
  public $role_permissions = []; // capturo nombres

  /**
   * *se ejecuta al renderizar el componente
   */
  public function mount()
  {
    $this->permissions = Permission::all();
    $this->roles = Role::all();
  }

  /**
   * * guardar un rol
   */
  public function save()
  {
    /* dd([
      'rol' => $this->role_name,
      'desc' => $this->role_short_description,
      'permisos' => $this->role_permissions
    ]); */

    //* validacion clasica
    /* $this->validate([
      'role_name' => 'required',
      'role_short_description' => 'required',
      'role_permissions' => 'required|array|min:1'
    ], [
      'role_name.required' => 'El nombre del rol es requerido'
    ], [
      'role_name' => 'nombre',
      'role_short_description' => 'descripcion corta',
      'role_permissions' => 'permisos del rol'
    ]); */

    //?validaciones desde reglas de livewire
    $this->validate();

    $new_role = Role::create([
      'name' => $this->role_name,
      'guard_name' => 'web',
      'short_description' => $this->role_short_description,
      'is_editable' => true,
      'is_internal' => true
    ]);

    $new_role->syncPermissions($this->role_permissions);

    // limpiar propiedades
    $this->reset(['role_name', 'role_short_description', 'role_permissions']);

    // refrescar los roles
    $this->roles = Role::all();
  }

  // renderiza el componente
  public function render()
  {
    return view('livewire.roles.create-role');
  }
}
