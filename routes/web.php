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

//* modulo usuarios
Route::get('users', [UserController::class, 'users_index'])->name('users-users-index');
Route::get('users/roles', [UserController::class, 'roles_index'])->name('users-roles-index');
Route::get('users/permissions', [UserController::class, 'permissions_index'])->name('users-permissions-index');
Route::get('users/role/create', [UserController::class, 'roles_create'])->name('users-roles-create');

require __DIR__.'/auth.php';
