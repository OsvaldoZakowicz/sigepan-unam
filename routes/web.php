<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Clients\ClientLogOutController;

//* layout publico
Route::view('/', 'welcome')->name('welcome');

//* layout interno
Route::middleware(['auth', 'verified', 'can:panel'])->group(function () {

  Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

  Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

});

//* modulo de usuarios
Route::middleware(['auth', 'verified', 'can:usuarios'])->group(function () {

  //* usuarios
  Route::get('users', [UserController::class, 'users_index'])->name('users-users-index');
  Route::get('users/create', [UserController::class, 'users_create'])->name('users-users-create');
  Route::get('users/edit/{id}', [UserController::class, 'users_edit'])->name('users-users-edit');
  Route::get('profile/complete', [UserController::class, 'profile_complete'])->name('profile-complete');

  //* roles
  Route::get('users/roles', [UserController::class, 'roles_index'])->name('users-roles-index');
  Route::get('users/role/create', [UserController::class, 'roles_create'])->name('users-roles-create');
  Route::get('users/role/edit/{id}', [UserController::class, 'roles_edit'])->name('users-roles-edit');

  //* permisos
  Route::get('users/permissions', [UserController::class, 'permissions_index'])->name('users-permissions-index');

});

//* clientes
Route::get('client/logout', ClientLogOutController::class)->name('client-logout');

require __DIR__.'/auth.php';
