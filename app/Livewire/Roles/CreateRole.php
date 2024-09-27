<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

/**
 * * clase CrearRol
 * actua como parte de un controlador
 */
class CreateRole extends Component
{
  //* propiedades accesibles
  // del modelo:
  public $permissions;

  //* propiedades accesibles de la vista
  // de la vista:
  public $role_name;
  public $role_short_description;
  public $role_permissions = []; // capturo nombres

  /**
   * *se ejecuta al renderizar el componente
   */
  public function mount()
  {
    $this->permissions = Permission::where('is_internal', true)->get();
  }

  /**
   * * guardar un rol
   */
  public function save()
  {
    //* validacion clasica
    $this->validate([
      'role_name' => ['required', Rule::unique('roles', 'name')],
      'role_short_description' => 'required',
      'role_permissions' => 'required|array|min:1'
    ], [
      'role_name.unique' => 'Ya existe un rol con el :attribute registrado'
    ], [
      'role_name' => 'nombre',
      'role_short_description' => 'descripcion corta',
      'role_permissions' => 'permisos del rol'
    ]);

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

    $this->redirectRoute('users-roles-index');
  }

  // renderiza el componente
  public function render()
  {
    return view('livewire.roles.create-role');
  }
}
