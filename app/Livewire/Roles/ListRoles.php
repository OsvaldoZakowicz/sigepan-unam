<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

class ListRoles extends Component
{
  // usamos paginacion
  use WithPagination;

  // busqueda
  #[Url]
  public $search = '';
  #[Url]
  public $editable = ''; // 0 = false, 1 = true
  #[Url]
  public $internal = ''; // 0 = false, 1 = true

  /**
   * * reiniciar la paginacion al inicio
   * permite que al buscar se inicie siempre desde el principio
   * si busco desde la pagina 2, 3, ...n, retorna al principio y luego busca
   */
  public function resetPagination()
  {
    $this->resetPage();
  }

  /**
   * * buscar roles
   * busca todos los roles paginados
   * filtra roles cuando los parametros de filtrado existen
   */
  public function searchRoles()
  {
    // roles paginados, incluyendo busqueda
    return Role::orderBy('id', 'desc')
      ->when($this->search, function ($query) {
        $query->where('id', 'like', '%'.$this->search.'%')
              ->orWhere('name', 'like', '%'.$this->search.'%');
      })
      ->when($this->editable, function ($query) {
        $query->where('is_editable', '=', $this->editable);
      })
      ->when($this->internal, function ($query) {
        $query->where('is_internal', '=', $this->internal);
      })
      ->paginate(10);
  }

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

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', false),
        'descr_toast' => 'Este rol no puede ser borrado, es un rol interno del sistema'
      ]);

      return;
    }

    // * CONTROL: el rol no tiene usuarios
    // haveUsers = false, puedo borrar el rol
    if (!$this->haveUsers($role->id)) {

      // quitar permisos del rol
      $role->syncPermissions([]);

      // borrar rol de forma segura
      $role->delete();

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'success',
        'title_toast' => toastTitle('exitosa'),
        'descr_toast' => toastSuccessBody('rol', 'eliminado')
      ]);

    } else {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', false),
        'descr_toast' => 'Este rol no puede ser borrado, tiene usuarios asignados'
      ]);

    }
  }

  public function render()
  {
    $roles = $this->searchRoles();

    return view('livewire.roles.list-roles', compact('roles'));
  }
}
