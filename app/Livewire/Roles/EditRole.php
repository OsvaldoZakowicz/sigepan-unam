<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class EditRole extends Component
{
  public $role;
  public $permissions;

  // propiedades del formulario
  public $role_name;
  public $role_short_description;
  public $role_permissions = [];

  public function mount($role_id)
  {
    $this->role = Role::findOrFail($role_id);

    if (!$this->role->is_editable) {
      // todo: mensaje toast los roles no editables no se editan
      return;
    }

    $this->permissions = Permission::where('is_internal', true)->get();

    //completo el formulario con los datos del rol
    $this->role_name = $this->role->name;
    $this->role_short_description = $this->role->short_description;
    $this->role_permissions = $this->role->permissions->pluck('name');
  }

  public function update()
  {
    $this->validate([
      'role_name' => ['required', Rule::unique('roles', 'name')->ignore($this->role->id)],
      'role_short_description' => 'required|min:15|max:150',
      'role_permissions' => 'required|array|min:1'
    ], [
      'role_name.unique' => 'Ya existe un rol con el :attribute registrado'
    ], [
      'role_name' => 'nombre',
      'role_short_description' => 'descripcion corta',
      'role_permissions' => 'permisos del rol'
    ]);

    /* dd([
      'role' => $this->role_name,
      'desc' => $this->role_short_description,
      'perm' => $this->role_permissions
    ]); */

    //actualizar
    $this->role->name = $this->role_name;
    $this->role->short_description = $this->role_short_description;
    $this->role->save();
    $this->role->syncPermissions($this->role_permissions);

    $this->reset(['role_name', 'role_short_description', 'role_permissions']);

    // todo: mensaje toast rol editado con exito
    $this->redirectRoute('users-roles-index');
  }

  public function render()
  {
    return view('livewire.roles.edit-role');
  }
}
