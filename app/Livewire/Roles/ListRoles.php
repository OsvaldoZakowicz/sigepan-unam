<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class ListRoles extends Component
{
  public $roles;

  public function mount()
  {
    $this->roles = Role::all();
  }

  protected function canDelete($role_id)
  {
    // first devuelve la primera ocurrencia, me basta una, o devuelve null
    $record = DB::table('model_has_roles')
    ->select('role_id')->where('role_id', '=', $role_id)
      ->first();

    // si es distinto de null, false (no puedo borrar), caso contrario true (puedo borrar)
    return ($record !== null) ? false : true;
  }

  public function delete($role_id)
  {
    $role = Role::findOrFail($role_id);

    // solo puedo borrar roles editables
    // is_editable = false (0) pasa a ser true, y retorno
    if (!$role->is_editable) {
      // todo: mensaje toast los roles no editables no se borran
      return;
    }

    // no puedo borrar un rol asignado a usuarios
    // si el id del rol figura en model_has_roles, no puedo borrarlo
    if ($this->canDelete($role->id)) {
      $role->syncPermissions([]); //quitar permisos
      $role->delete();
      // refrescar lista de roles
      $this->roles = Role::all();
    } else {
      // todo: mensaje toast, no se puede eliminar un rol con usuarios asignados
    }
  }

  public function render()
  {
    return view('livewire.roles.list-roles');
  }
}
