<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class ListRoles extends Component
{
  // usamos paginacion
  use WithPagination;

  /**
   * * el rol tiene usuarios?
   * un rol tiene muchos usuarios asignados
   * para poder borrar, debo verificar que no tenga usuarios asignados.
   */
  protected function haveUsers($role_id)
  {
    // * veamos la tabla intermedia model_has_roles, las foreign key son "on delete restrict"
    // si el rol aparece al menos una vez en la tabla intermedia, VERIFICA que tiene usuarios signados.
    $record = DB::table('model_has_roles')
      ->select('role_id')->where('role_id', '=', $role_id)->first();

    // si es distinto de null, haveUsers = true, haveUsers = false
    return ($record !== null) ? true : false;
  }

  /**
   * * puedo borrar el rol?
   * un rol tiene muchos permisos, y muchos usuarios asignados
   * para poder borrar, debo verificar que no tenga usuarios asignados.
   *
   */
  public function delete($role_id)
  {
    $role = Role::findOrFail($role_id);

    // solo puedo borrar roles editables
    // is_editable = false (0) pasa a ser true, y retorno
    if (!$role->is_editable) {
      // todo: mensaje toast los roles no editables no se borran
      return;
    }

    // * CONTROL: el rol no tiene usuarios
    // haveUsers = false, puedo borrar el rol
    if (!$this->haveUsers($role->id)) {

      //* CONTROL: el rol no tiene permisos
      // quitar permisos del rol
      $role->syncPermissions([]);

      // borrar rol de forma segura
      $role->delete();

    } else {
      // todo: mensaje toast, no se puede eliminar un rol con usuarios asignados
    }
  }

  public function render()
  {
    // roles paginados
    $roles = Role::orderBy('id', 'desc')->paginate(10);

    return view('livewire.roles.list-roles', compact('roles'));
  }
}
