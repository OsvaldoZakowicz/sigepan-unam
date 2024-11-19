<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Clients\ClientLogOutController;
use App\Http\Controllers\Audits\AuditController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Http\Controllers\Stocks\StockController;

/**
 * * NOTA aplicar permisos de acceso via middleware a:
 * - modulos por su nombre (mostrando el index)
 * - vistas por su nombre (perfil por ejemplo)
 */

//* layout publico
Route::view('/', 'welcome')->name('welcome');

//* layout interno panel
Route::middleware(['auth', 'verified'])->group(function () {

  // vista del panel
  Route::view('dashboard', 'resume-dashboard')
    ->middleware(['can:panel'])
    ->name('dashboard');

  // vista del perfil, actualizar usuario y password, borrar cuenta
  Route::view('profile', 'profile')
    ->middleware(['can:panel','can:panel-perfil'])
    ->name('profile');

  // vista de completar y/o editar perfil
  Route::get('profile/complete', [UserController::class, 'profile_complete'])
    ->middleware(['can:panel','can:panel-perfil'])
    ->name('profile-complete');

});

//* modulo de usuarios
Route::middleware(['auth', 'verified', 'can:usuarios'])->group(function () {

  //* usuarios
  Route::get('users', [UserController::class, 'users_index'])
    ->name('users-users-index');

  Route::get('users/create', [UserController::class, 'users_create'])
    ->name('users-users-create');

  Route::get('users/edit/{id}', [UserController::class, 'users_edit'])
    ->name('users-users-edit');

  //* roles
  Route::get('users/roles', [UserController::class, 'roles_index'])
    ->name('users-roles-index');

  Route::get('users/role/create', [UserController::class, 'roles_create'])
    ->name('users-roles-create');

  Route::get('users/role/edit/{id}', [UserController::class, 'roles_edit'])
    ->name('users-roles-edit');

  //* permisos
  Route::get('users/permissions', [UserController::class, 'permissions_index'])
    ->name('users-permissions-index');

});

//* modulo de auditoria
Route::middleware(['auth', 'verified', 'can:auditoria'])->group(function () {

  Route::get('audits', [AuditController::class, 'audits_index'])
    ->name('audits-audits-index');

  Route::get('audits/show/{id}', [AuditController::class, 'audits_show'])
    ->name('audits-audits-show');

  Route::get('audits/report/one/{id}', [AuditController::class, 'audits_report_one'])
    ->name('audits-audits-report');

});

//* modulo de proveedores
Route::middleware(['auth', 'verified', 'can:proveedores'])->group(function () {

  Route::get('suppliers', [SupplierController::class, 'suppliers_index'])
    ->name('suppliers-suppliers-index');

  Route::get('suppliers/create', [SupplierController::class, 'suppliers_create'])
    ->name('suppliers-suppliers-create');

  Route::get('supplier/show/{id}', [SupplierController::class, 'suppliers_show'])
    ->name('suppliers-suppliers-show');

  Route::get('supplier/edit/{id}', [SupplierController::class, 'suppliers_edit'])
    ->name('suppliers-suppliers-edit');

  Route::get('suppliers/provisions', [SupplierController::class, 'provisions_index'])
    ->name('suppliers-provisions-index');

  Route::get('suppliers/provision/create', [SupplierController::class, 'provisions_create'])
    ->name('suppliers-provisions-create');

  Route::get('suppliers/provision/edit/{id}', [SupplierController::class, 'provisions_edit'])
    ->name('suppliers-provisions-edit');

  // NOTA: mantengo la ruta con nombre suppliers-suppliers-*
  Route::get('supplier/{id}/provisions/prices', [SupplierController::class, 'suppliers_price_list'])
    ->name('suppliers-suppliers-price-index');

  // NOTA: mantengo la ruta con nombre suppliers-suppliers-*
  Route::get('supplier/{id}/provision/prices/create', [SupplierController::class, 'suppliers_add_provision_price_to_provisions_price_list'])
    ->name('suppliers-suppliers-price-create');

  // NOTA: lista de todos los precios
  Route::get('suppliers/prices', [SupplierController::class, 'suppliers_all_prices_list'])
    ->name('suppliers-suppliers-price-all');

  Route::get('suppliers/trademarks', [SupplierController::class, 'trademarks_index'])
    ->name('suppliers-trademarks-index');

  Route::get('suppliers/trademark/create', [SupplierController::class, 'trademarks_create'])
    ->name('suppliers-trademarks-create');

});

//* modulo de stock
Route::middleware(['auth', 'verified', 'can:stock'])->group(function () {

  Route::get('stocks', [StockController::class, 'stocks_index'])
    ->name('stocks-stocks-index');

  Route::get('stocks/measures', [StockController::class, 'measures_index'])
    ->name('stocks-measures-index');

  Route::get('stocks/measures/create', [StockController::class, 'measures_create'])
    ->name('stocks-measures-create');

});

//* modulo de clientes
Route::get('client/logout', ClientLogOutController::class)
  ->name('client-logout');

require __DIR__.'/auth.php';
