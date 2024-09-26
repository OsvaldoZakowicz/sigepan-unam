<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

//* modulo usuarios: usuarios
Route::get('users', [UserController::class, 'users_index'])->name('users-users-index');
Route::get('users/create', [UserController::class, 'users_create'])->name('users-users-create');
Route::get('users/edit/{id}', [UserController::class, 'users_edit'])->name('users-users-edit');

//* modulo usuarios: roles
Route::get('users/roles', [UserController::class, 'roles_index'])->name('users-roles-index');
Route::get('users/role/create', [UserController::class, 'roles_create'])->name('users-roles-create');

//* modulo usuarios: permisos
Route::get(
  'users/permissions',
  [UserController::class, 'permissions_index']
)->name('users-permissions-index');

require __DIR__.'/auth.php';
