<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * * controlador del modulo de usuarios
 */
class UserController extends Controller
{
  /**
   * * index principal del modulo de usuarios
   */
  public function users_index(): View
  {
    return view('users.users-index');
  }

  /**
   * * index principal al apartado de roles
   */
  public function roles_index(): View
  {
    return view('users.roles-index');
  }

  /**
   * * index principal al apartado de permisos
   */
  public function permissions_index(): View
  {
    return view('users.permissions-index');
  }

  /**
   * * crear un rol
   */
  public function roles_create(): View
  {
    return view('users.roles-create');
  }

  /**
   * * crear un usuario interno
   */
  public function users_create(): view
  {
    return view('users.users-create');
  }

  /**
   * * editar un usuario interno
   */
  public function users_edit($id): View
  {
    return view('users.users-edit', ['id' => $id]);
  }
}
