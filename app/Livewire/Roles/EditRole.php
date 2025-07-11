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

  // roles por defecto
  public $permissions_default_names = ['panel', 'panel-perfil'];
  public $permissions_default = [];

  // permisos no elegibles
  public $permissions_excluded = ['presupuestos', 'ordenes'];

  public function mount($role_id)
  {
    $this->role = Role::findOrFail($role_id);

    // no puedo editar un rol cuando tiene is_editable = false
    if (!$this->role->is_editable) {
      session()->flash('operation-info', 'Este rol no puede ser editado, es un rol interno del sistema');
      $this->redirectRoute('users-roles-index');
    }

    $merged = array_merge($this->permissions_default_names, $this->permissions_excluded);

    $this->permissions = Permission::where('is_internal', true)
      ->whereNotIn('name', $merged)
      ->get();

    $this->permissions_default = Permission::whereIn('name', $this->permissions_default_names)
      ->get();

    //completo el formulario con los datos del rol
    $this->role_name = $this->role->name;
    $this->role_short_description = $this->role->short_description;
    $this->role_permissions = $this->role->permissions->pluck('name')->toArray();
  }

  public function update()
  {
    $this->validate([
      'role_name' => ['required', Rule::unique('roles', 'name')->ignore($this->role->id), 'regex:/^[a-zA-Z\s]+$/u'],
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

    //actualizar
    $this->role->name = $this->role_name;
    $this->role->short_description = $this->role_short_description;
    $this->role->save();
    $this->role->syncPermissions($this->role_permissions);

    $this->reset(['role_name', 'role_short_description', 'role_permissions']);

    session()->flash('operation-success', toastSuccessBody('rol', 'editado'));
    $this->redirectRoute('users-roles-index');
  }

  public function render()
  {
    return view('livewire.roles.edit-role');
  }
}
