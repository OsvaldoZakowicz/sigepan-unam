<?php

use App\Jobs\ClosePreOrderPeriodJob;
use App\Jobs\CloseQuotationPeriodJob;
use App\Jobs\NotifySuppliersRequestForPreOrderClosedJob;
use App\Jobs\NotifySuppliersRequestForPreOrderReceivedJob;
use App\Jobs\NotifySuppliersRequestForQuotationClosedJob;
use App\Jobs\NotifySuppliersRequestForQuotationReceivedJob;
use App\Jobs\OpenPreOrderPeriodJob;
use App\Jobs\OpenQuotationPeriodJob;
use App\Jobs\SendEmailJob;
use App\Mail\CantClosePreOrderPeriod;
use App\Mail\ClosePreOrderPeriod;
use App\Services\Supplier\QuotationPeriodService;
use App\Services\Supplier\PreOrderPeriodService;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

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
      Bus::chain([
        OpenQuotationPeriodJob::dispatch($period),
        NotifySuppliersRequestForQuotationReceivedJob::dispatch($period)
      ]);
    }
  }
})->purpose('abrir periodos de solicitud de presupuestos')
  ->everyMinute();

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
      Bus::chain([
        CloseQuotationPeriodJob::dispatch($period),
        NotifySuppliersRequestForQuotationClosedJob::dispatch($period),
      ]);
    }
  }
})->purpose('cerrar periodos de solicitud de presupuestos')
  ->everyTwoMinutes();

/**
 * command: abrir periodos de pre orden.
 * @param string $signature
 * @param \Closure $callback
 */
Artisan::command('preorder-periods:open', function (PreOrderPeriodService $preorder_period_service) {

  $periods_to_open = $preorder_period_service->getPreOrderPeriodsToOpen();

  if (count($periods_to_open) != 0) {
    // cada periodo debe abrirse y procesarse
    foreach ($periods_to_open as $preorder_period) {
      Bus::chain([
        OpenPreOrderPeriodJob::dispatch($preorder_period),
        NotifySuppliersRequestForPreOrderReceivedJob::dispatch($preorder_period),
      ]);
    }
  }
})->purpose('abrir periodos de solicitud de pre ordenes')
  ->everyMinute();

/**
 * command: cerrar periodos de pre orden
 * @param string $signature
 * @param \Closure $callback
 */
Artisan::command('preorder-periods:close', function (PreOrderPeriodService $preorder_period_service) {

  $periods_to_close = $preorder_period_service->getPreOrdersPeriodsToClose();

  $gerentes_to_notify = User::role('gerente')->get();

  if (count($periods_to_close) != 0) {

    foreach ($periods_to_close as $preorder_period) {

      if ($preorder_period_service->getPreOrdersPending($preorder_period)) {

        // notificar de la imposibilidad de cierre del periodo a gerentes
        foreach ($gerentes_to_notify as $gerente) {
          SendEmailJob::dispatch($gerente->email, new CantClosePreOrderPeriod($preorder_period));
        }

      } else {

        // cerrar periodo
        Bus::chain([
          ClosePreOrderPeriodJob::dispatch($preorder_period),
          NotifySuppliersRequestForPreOrderClosedJob::dispatch($preorder_period),
        ]);

        // notificar del cierre a gerentes
        foreach ($gerentes_to_notify as $gerente) {
          SendEmailJob::dispatch($gerente->email, new ClosePreOrderPeriod($this->preorder_period));
        }
      }

    }
  }
})->purpose('cerrar periodos de solicitud de pre orden')
  ->everyTwoMinutes();
