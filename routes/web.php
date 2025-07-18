<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Clients\ClientLogOutController;
use App\Http\Controllers\Audits\AuditController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Http\Controllers\Stocks\StockController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Store\StoreController;
use App\Http\Controllers\Pdf\PDFController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Stats\StatsController;

//* layout publico
// retorna la vista tienda publica
Route::get('store', [StoreController::class, 'store_index'])
  ->middleware('restrict.non.clients')
  ->name('store-store-index');

//* modulo de clientes
// acceso solo a rol cliente
Route::middleware(['auth', 'can:tienda', 'can:tienda-perfil'])->group(function () {

  // acceso a la ruta para perfil de cliente en tienda

  // carrito de compras para el pago
  // muestra el pedido como una orden ya realizada (id de orden)
  Route::get('store/cart/{id}', [PaymentController::class, 'cart_index'])
    ->middleware('verified')
    ->name('store-store-cart-index');

  // pedidos del cliente
  Route::get('store/orders', [StoreController::class, 'orders_list'])
    ->middleware('verified')
    ->name('store-store-orders-list');

  // compras del cliente
  Route::get('store/purchases', [StoreController::class, 'purchases_list'])
    ->middleware('verified')
    ->name('store-store-purchases-list');

  // compra exitosa
  Route::get('store/payment/success', [PaymentController::class, 'payment_success'])
    ->middleware('verified')
    ->name('store-store-payment-success');

  // compra fallida
  Route::get('store/payment/failure', [PaymentController::class, 'payment_failure'])
    ->middleware('verified')
    ->name('store-store-payment-failure');

  // compra pendiente
  Route::get('store/payment/pending', [PaymentController::class, 'payment_pending'])
    ->middleware('verified')
    ->name('store-store-payment-pending');

  // vista del perfil CLIENTE, actualizar usuario y password, borrar cuenta
  Route::view('profile/client', 'profile-client')
    ->middleware(['can:tienda', 'can:tienda-perfil'])
    ->name('profile-client');

  Route::get('profile/client/complete', [UserController::class, 'profile_client_complete'])
    ->middleware(['can:tienda', 'can:tienda-perfil'])
    ->name('profile-client-complete');

  // * redirecciona a / y retorna 'welcome'
  Route::get('client/logout', ClientLogOutController::class)
    ->name('client-logout');

  //*---------------------------- PDFs

  // * NOTA: PDF de venta es el mismo que pdf de compra del cliente, pero va por otra ruta
  Route::get('store/purchases/pdf/open/{id}', [PDFController::class, 'open_pdf_sale'])
    ->name('open-pdf-purchase');
});

//* layout interno panel
Route::middleware(['auth', 'verified'])->group(function () {

  // vista del panel
  Route::view('dashboard', 'resume-dashboard')
    ->middleware(['can:panel'])
    ->name('dashboard');

  // vista del perfil, actualizar usuario y password, borrar cuenta
  Route::view('profile', 'profile')
    ->middleware(['can:panel', 'can:panel-perfil'])
    ->name('profile');

  // vista de completar y/o editar perfil
  Route::get('profile/complete', [UserController::class, 'profile_complete'])
    ->middleware(['can:panel', 'can:panel-perfil'])
    ->name('profile-complete');
});

// * modulo de estadisticas
Route::middleware(['auth', 'verified', 'can:estadisticas'])->group(function () {

  Route::get('stats', [StatsController::class, 'stats_index'])
    ->name('stats-stats-index');

  //* ---------------------------------------------------- pdfs

  Route::get('stats/salestat', [PDFController::class, 'stream_pdf_sale_stat'])
    ->name('open-pdf-stat');
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

  Route::get('audits/show/history/{id}', [AuditController::class, 'audits_show_history'])
    ->name('audits-audits-show-history');

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

  // NOTA: mantengo la ruta con nombre suppliers-suppliers-*
  Route::get('supplier/{id}/provision/prices/edit', [SupplierController::class, 'suppliers_edit_provision_price_to_provisions_price_list'])
    ->name('suppliers-suppliers-price-edit');

  // NOTA: lista de todos los precios
  Route::get('suppliers/prices', [SupplierController::class, 'suppliers_all_prices_list'])
    ->name('suppliers-suppliers-price-all');

  Route::get('suppliers/categories', [SupplierController::class, 'categories_index'])
    ->name('suppliers-categories-index');

  Route::get('suppliers/category/create', [SupplierController::class, 'categories_create'])
    ->name('suppliers-categories-create');

  Route::get('suppliers/category/edit/{id}', [SupplierController::class, 'categories_edit'])
    ->name('suppliers-categories-edit');

  Route::get('suppliers/trademarks', [SupplierController::class, 'trademarks_index'])
    ->name('suppliers-trademarks-index');

  Route::get('suppliers/trademark/create', [SupplierController::class, 'trademarks_create'])
    ->name('suppliers-trademarks-create');

  Route::get('suppliers/trademark/edit/{id}', [SupplierController::class, 'trademarks_edit'])
    ->name('suppliers-trademarks-edit');

  // NOTA: listar periodos de presupuestos (presupuesto = budget, periodo = period)
  Route::get('suppliers/budgets/periods', [SupplierController::class, 'budget_periods_index'])
    ->name('suppliers-budgets-periods-index');

  // NOTA: crear periodo de presupuesto (presupuesto = budget, periodo = period)
  Route::get('suppliers/budgets/periods/create', [SupplierController::class, 'budget_periods_create'])
    ->name('suppliers-budgets-periods-create');

  // NOTA: ver periodo de presupuesto (presupuesto = budget, periodo = period)
  Route::get('suppliers/budget/periods/show/{id}', [SupplierController::class, 'budget_periods_show'])
    ->name('suppliers-budgets-periods-show');

  // NOTA: editar periodo de presupuesto y reabrirlo
  Route::get('suppliers/budget/periods/edit/{id}', [SupplierController::class, 'budget_periods_edit'])
    ->name('suppliers-budgets-periods-edit');

  // NOTA: ver respuesta de un proveedor a un presupuesto en el periodo
  Route::get('suppliers/budget/response/{id}', [SupplierController::class, 'budget_response'])
    ->name('suppliers-budgets-response');

  // NOTA: ver ranking de presupuestos respondidos, recibe id de periodo en cuestion
  Route::get('suppliers/budget/ranking/{id}', [SupplierController::class, 'budget_ranking'])
    ->name('suppliers-budgets-ranking');

  // NOTA: listar periodos de pre orden
  Route::get('suppliers/preorders', [SupplierController::class, 'preorder_index'])
    ->name('suppliers-preorders-index');

  // NOTA: crear periodo de pre ordenes, opcionalmente a partir de un periodo de presupuestos
  Route::get('suppliers/preorders/create/{id?}', [SupplierController::class, 'preorder_create'])
    ->name('suppliers-preorders-create');

  // NOTA: editar periodo de pre ordenes (solo los creados manualmente)
  Route::get('suppliers/preorders/edit/{id}', [SupplierController::class, 'preorder_edit'])
    ->name('suppliers-preorders-edit');

  // NOTA: ver periodo de pre ordenes
  Route::get('suppliers/preorders/show/{id}', [SupplierController::class, 'preorder_show'])
    ->name('suppliers-preorders-show');

  // NOTA: ver la respuesta de una pre orden del proveedor
  Route::get('suppliers/preorders/response/{id}', [SupplierController::class, 'preorder_response'])
    ->name('suppliers-preorders-response');

  //*---------------------------- PDFs

  Route::get('preorders-supplier/pdf/show/{id}', [PDFController::class, 'open_pdf_order'])
    ->name('open-pdf-order');

  Route::get('quotations-supplier/pdf/stream/{id}', [PDFController::class, 'stream_pdf_quotation'])
    ->name('open-pdf-quotation-supplier');
});

//* seccion de presupuestos (quotations) y preordenes para proveedores
Route::middleware(['auth', 'verified', 'can:presupuestos'])->group(function () {

  Route::get('quotations', [QuotationController::class, 'quotations_index'])
    ->name('quotations-quotations-index');

  Route::get('quotations/respond/{id}', [QuotationController::class, 'quotations_respond'])
    ->name('quotations-quotations-respond');

  Route::get('quotations/edit/{id}', [QuotationController::class, 'quotations_edit'])
    ->name('quotations-quotations-edit');

  Route::get('quotations/show/{id}', [QuotationController::class, 'quotations_show'])
    ->name('quotations-quotations-show');

  Route::get('preorders', [QuotationController::class, 'preorders_index'])
    ->name('quotations-preorders-index');

  Route::get('preorders/respond/{id}', [QuotationController::class, 'preorders_respond'])
    ->name('quotations-preorders-respond');

  Route::get('preorders/show/{id}', [QuotationController::class, 'preorders_show'])
    ->name('quotations-preorders-show');

  //*---------------------------- PDFs

  Route::get('preorders/pdf/download/{id}', [PDFController::class, 'download_pdf_order'])
    ->name('download-pdf-order');

  Route::get('quotations/pdf/stream/{id}', [PDFController::class, 'stream_pdf_quotation'])
    ->name('open-pdf-quotation');
});

//* modulo de stock
Route::middleware(['auth', 'verified', 'can:stock'])->group(function () {

  Route::get('stocks/measures', [StockController::class, 'measures_index'])
    ->name('stocks-measures-index');

  Route::get('stocks/measures/create', [StockController::class, 'measures_create'])
    ->name('stocks-measures-create');

  Route::get('stocks/recipes', [StockController::class, 'recipes_index'])
    ->name('stocks-recipes-index');

  Route::get('stocks/recipes/create', [StockController::class, 'recipes_create'])
    ->name('stocks-recipes-create');

  Route::get('stocks/recipes/edit/{id}', [StockController::class, 'recipes_edit'])
    ->name('stocks-recipes-edit');

  Route::get('stocks/recipes/show/{id}', [StockController::class, 'recipes_show'])
    ->name('stocks-recipes-show');

  Route::get('stocks/products', [StockController::class, 'products_index'])
    ->name('stocks-products-index');

  Route::get('stocks/products/create', [StockController::class, 'products_create'])
    ->name('stocks-products-create');

  // mostrar detalle del producto
  Route::get('stocks/products/show/{id}', [StockController::class, 'products_show'])
    ->name('stocks-products-show');

  // mostrar stock del procucto
  Route::get('stocks/products/product/{id}/stock', [StockController::class, 'product_stock'])
    ->name('stocks-products-product-stock');

  Route::get('stocks/products/edit/{id}', [StockController::class, 'products_edit'])
    ->name('stocks-products-edit');

  Route::get('stocks/existences', [StockController::class, 'existences_index'])
    ->name('stocks-existences-index');

  Route::get('stocks/tags', [StockController::class, 'tags_index'])
    ->name('stocks-tags-index');

  Route::get('stocks/tags/create', [StockController::class, 'tags_create'])
    ->name('stocks-tags-create');

  Route::get('stocks/tags/edit/{id}', [StockController::class, 'tags_edit'])
    ->name('stocks-tags-edit');
});

//* modulo de ventas
Route::middleware(['auth', 'verified', 'can:ventas'])->group(function () {

  Route::get('sales', [SaleController::class, 'sales_index'])
    ->name('sales-sales-index');

  Route::get('sales/orders', [SaleController::class, 'sales_orders_index'])
    ->name('sales-orders-index');

  //*---------------------------- PDFs
  Route::get('sales/pdf/open/{id}', [PDFController::class, 'open_pdf_sale'])
    ->name('open-pdf-sale');
});

//* modulo de compras
Route::middleware(['auth', 'verified', 'can:compras'])->group(function () {

  Route::get('purchases', [PurchaseController::class, 'purchases_index'])
    ->name('purchases-purchases-index');

  // NOTA: registrar una compra con una preorden (id de preorden opcional)
  Route::get('purchases/create/{id?}', [PurchaseController::class, 'purchases_create'])
    ->name('purchases-purchases-create');

  Route::get('purchases/preorders', [PurchaseController::class, 'purchases_preorders_index'])
    ->name('purchases-preorders-index');

  //*---------------------------- PDFs

  Route::get('purchases/details/order/pdf/show/{id}', [PDFController::class, 'open_pdf_order'])
    ->name('open-pdf-order-from-purchase');
});


require __DIR__ . '/auth.php';
