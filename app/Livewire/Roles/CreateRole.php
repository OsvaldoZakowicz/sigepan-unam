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
  // permisos seleccionables
  public $permissions;

  public $role_name;
  public $role_short_description;

  // capturo nombres
  public $role_permissions = [];

  // permisos por defecto
  public $permissions_default_names = ['panel', 'panel-perfil'];
  public $permissions_default = [];

  // permisos no elegibles
  public $permissions_excluded = ['presupuestos', 'ordenes'];

  //* montar datos
  public function mount()
  {
    $merged = array_merge($this->permissions_default_names, $this->permissions_excluded);

    // permisos que no son por defecto
    $this->permissions = Permission::where('is_internal', true)
        ->whereNotIn('name', $merged)
        ->get();

    // permisos por defecto
    $this->permissions_default = Permission::whereIn('name', $this->permissions_default_names)
      ->get();
  }

  /**
   * * guardar un rol
   */
  public function save()
  {
    //* validacion clasica
    $this->validate([
      'role_name' => ['required', Rule::unique('roles', 'name'), 'regex:/^[a-zA-Z\s]+$/u'],
      'role_short_description' => 'required|min:15|max:150',
      'role_permissions' => 'required|array|min:1'
    ], [
      'role_name.unique' => 'Ya existe un rol con el :attribute registrado',
      'role_name.regex' => 'El :attribute debe contener letras o espacios solamente'
    ], [
      'role_name' => 'nombre',
      'role_short_description' => 'descripcion corta',
      'role_permissions' => 'permisos del rol'
    ]);

    $new_role = Role::create([
      'name'              => $this->role_name,
      'guard_name'        => 'web',
      'short_description' => $this->role_short_description,
      'is_editable' => true,
      'is_internal' => true
    ]);

    // completar con permisos por defecto
    foreach ($this->permissions_default as $permission) {
      array_push($this->role_permissions, $permission->name);
    }

    $new_role->syncPermissions($this->role_permissions);

    // limpiar propiedades
    $this->reset();

    session()->flash('operation-success', toastSuccessBody('rol', 'creado'));
    $this->redirectRoute('users-roles-index');
  }

  // renderiza el componente
  public function render()
  {
    return view('livewire.roles.create-role');
  }
}
