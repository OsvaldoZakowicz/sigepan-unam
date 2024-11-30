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
*/
Artisan::command('budget-periods:open', function (BudgetPeriodService $bps) {

  // periodos cuya fecha de inicio sea igual a la fecha actual, y estatus programado
  $periods_to_open = $bps->getQuotationPeriodsToOpen();

  // cada periodo debe abrirse y procesarse
  foreach ($periods_to_open as $period) {

    $period->period_status_id = $bps->getStatusOpen();
    $period->save();

  }
})->purpose('abrir periodos de peticion de presupuestos')->everyMinute();

/**
 * * command: cerrar periodos de peticion de presupuesto.
 * @param signature: firma del comando (nombre y argumentos opcionales)
 * @param clusure: callback fn con la logica o servicio del comando.
 */
Artisan::command('budget-periods:close', function (BudgetPeriodService $bps) {

  // periodos cuya fecha de fin sea igual a la fecha actual, y estatus abierto
  $periods_to_close = $bps->getQuotationPeriodsToClose();

  // cada periodo debe cerrarse y procesarse
  foreach ($periods_to_close as $period) {

    $period->period_status_id = $bps->getStatusClosed();
    $period->save();

  }
})->purpose('cerrar periodos de peticion de presupuestos')->everyMinute();
