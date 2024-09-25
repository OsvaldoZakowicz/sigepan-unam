<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
   * * crear un rol
   */
  public function roles_create(): View
  {
    return view('users.roles-create');
  }
}
