<?php

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\BudgetPeriodService;
use Illuminate\Support\Facades\Artisan;

/**
 * * clousure commands.
 * https://laravel.com/docs/11.x/artisan#closure-commands
 *
 * ->purpose(); muestra una descripcion al ejecutar del comando al ejecutar
 * sail artisan list o sail artisan help.
 *
 * * scheduling clousure commands.
 * * listar comandos: sail artisan schedule:list
 * * correr comandos: sail artisan schedule:work
 * https://laravel.com/docs/11.x/scheduling#scheduling-artisan-closure-commands
 * ->everyMinute(); ejecutar el comando cada minuto.
 * ->everyTwoMinutes(); ejecutar el comando cada dos minutos.
 * ->everyFiveMinutes(); ejecutar el comando cada cinco minutos.
 */

/**
 * * command: abrir periodos de peticion de presupuesto.
 * @param signature: firma del comando (nombre y argumentos opcionales)
 * @param clusure: callback fn con la logica o servicio del comando.
 * todo: ejecutar cada minuto?
 */
Artisan::command('budget-periods:open', function (BudgetPeriodService $bps) {
  // fecha actual
  $today = now()->format('Y-m-d');

  // periodos cuya fecha de inicio sea igual a la fecha actual, y estatus programado
  $periods = RequestForQuotationPeriod::whereHas('status', function ($query) {
    $query->where('status_name', 'programado');
  })->where('period_start_at', $today)
    ->get();

  // cada periodo debe abrirse
  // todo: cada periodo abierto debe contactar a sus proveedores
  foreach ($periods as $period) {
    $period->period_status_id = $bps->getStatusOpen();
    $period->save();
  }
})->purpose('abrir periodos de peticion de presupuestos')->everyMinute();

/**
 * * command: cerrar periodos de peticion de presupuesto.
 * todo: ejecutar cada dos minutos?
 */
Artisan::command('budget-periods:close', function (BudgetPeriodService $bps) {
  // fecha actual
  $today = now()->format('Y-m-d');

  // periodos cuya fecha de fin sea igual a la fecha actual, y estatus abierto
  $periods = RequestForQuotationPeriod::whereHas('status', function ($query) {
    $query->where('status_name', 'abierto');
  })->where('period_end_at', $today)
    ->get();

  // cada periodo debe cerrarse
  // todo: cada periodo abierto debe cerrar, y dejar de contactar con proveedores
  foreach ($periods as $period) {
    $period->period_status_id = $bps->getStatusClosed();
    $period->save();
  }
})->purpose('cerrar periodos de peticion de presupuestos')->everyTwoMinutes();
