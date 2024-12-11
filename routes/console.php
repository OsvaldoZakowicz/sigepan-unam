<?php

use App\Jobs\CloseQuotationPeriodJob;
use App\Services\Supplier\QuotationPeriodService;
use App\Jobs\OpenQuotationPeriodJob;
use Illuminate\Support\Facades\Artisan;

/**
 * * clousure commands.
 * https://laravel.com/docs/11.x/artisan#closure-commands
 *
 * ->purpose(); muestra una descripcion al ejecutar del comando al ejecutar
 * sail artisan list o sail artisan help.
 *
 * * scheduling clousure commands.
 * listar comandos: sail artisan schedule:list
 * correr comandos: sail artisan schedule:work
 * https://laravel.com/docs/11.x/scheduling#scheduling-artisan-closure-commands
 * ->everyMinute(); ejecutar el comando cada minuto.
 * ->everyTwoMinutes(); ejecutar el comando cada dos minutos.
 * ->everyFiveMinutes(); ejecutar el comando cada cinco minutos.
*/

/**
 * command: abrir periodos de peticion de presupuesto.
 * @param signature: firma del comando (nombre y argumentos opcionales)
 * @param clusure: callback fn con la logica o servicio del comando.
*/
Artisan::command('budget-periods:open', function (QuotationPeriodService $quotation_period_service) {

  $periods_to_open = $quotation_period_service->getQuotationPeriodsToOpen();

  if (count($periods_to_open) != 0) {
    // cada periodo debe abrirse y procesarse
    foreach ($periods_to_open as $period) {
      OpenQuotationPeriodJob::dispatch($period);
    }
  }
})->purpose('abrir periodos de peticion de presupuestos')->everyMinute();

/**
 * command: cerrar periodos de peticion de presupuesto.
 * @param signature: firma del comando (nombre y argumentos opcionales)
 * @param clusure: callback fn con la logica o servicio del comando.
*/
Artisan::command('budget-periods:close', function (QuotationPeriodService $quotation_period_service) {

  $periods_to_close = $quotation_period_service->getQuotationPeriodsToClose();

  if (count($periods_to_close) != 0) {
    // cada periodo debe cerrarse y procesarse
    foreach ($periods_to_close as $period) {
      CloseQuotationPeriodJob::dispatch($period);
    }
  }
})->purpose('cerrar periodos de peticion de presupuestos')->everyTwoMinutes();
