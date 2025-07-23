<?php

use App\Models\User;
use App\Jobs\SendEmailJob;
use App\Mail\ClosePreOrderPeriod;
use App\Mail\CloseQuotationPeriod;
use Illuminate\Support\Facades\DB;
use App\Jobs\OpenPreOrderPeriodJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Jobs\ClosePreOrderPeriodJob;
use App\Jobs\OpenQuotationPeriodJob;
use App\Jobs\CloseQuotationPeriodJob;
use App\Mail\CantClosePreOrderPeriod;
use App\Jobs\UpdateSuppliersPricesJob;
use Illuminate\Support\Facades\Artisan;
use App\Services\Supplier\PreOrderPeriodService;
use App\Services\Supplier\QuotationPeriodService;
use App\Jobs\NotifySuppliersRequestForPreOrderClosedJob;
use App\Jobs\NotifySuppliersRequestForQuotationClosedJob;
use App\Jobs\NotifySuppliersRequestForPreOrderReceivedJob;
use App\Jobs\NotifySuppliersRequestForQuotationReceivedJob;
use App\Models\StockMovement;
use App\Services\Audits\AuditService;
use App\Services\Stock\StockService;
use Illuminate\Support\Facades\Auth;

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
        OpenQuotationPeriodJob::dispatch($period->id),
        NotifySuppliersRequestForQuotationReceivedJob::dispatch($period->id)
      ]);
    }
  }
})->purpose('abrir periodos de solicitud de presupuestos')
  ->dailyAt('08:00');

/**
 * command: cerrar periodos de peticion de presupuesto.
 * @param signature: firma del comando (nombre y argumentos opcionales)
 * @param clusure: callback fn con la logica o servicio del comando.
 */
Artisan::command('budget-periods:close', function (QuotationPeriodService $quotation_period_service) {

  $periods_to_close = $quotation_period_service->getQuotationPeriodsToClose();

  $gerentes_to_notify = User::role('gerente')->get();

  if (count($periods_to_close) != 0) {

    // cada periodo debe cerrarse y procesarse
    foreach ($periods_to_close as $period) {

      Bus::chain([
        CloseQuotationPeriodJob::dispatch($period->id),
        UpdateSuppliersPricesJob::dispatch($period->id),
        NotifySuppliersRequestForQuotationClosedJob::dispatch($period->id),
      ]);

      // notificar del cierre a gerentes
      foreach ($gerentes_to_notify as $gerente) {
        SendEmailJob::dispatch($gerente->email, new CloseQuotationPeriod($period));
      }
    }
  }
})->purpose('cerrar periodos de solicitud de presupuestos')
  ->dailyAt('19:00');

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
        OpenPreOrderPeriodJob::dispatch($preorder_period->id),
        NotifySuppliersRequestForPreOrderReceivedJob::dispatch($preorder_period->idate),
      ]);
    }
  }
})->purpose('abrir periodos de solicitud de pre ordenes')
  ->dailyAt('08:00');

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
          ClosePreOrderPeriodJob::dispatch($preorder_period->id),
          NotifySuppliersRequestForPreOrderClosedJob::dispatch($preorder_period->id),
        ]);

        // notificar del cierre a gerentes
        foreach ($gerentes_to_notify as $gerente) {
          SendEmailJob::dispatch($gerente->email, new ClosePreOrderPeriod($this->preorder_period));
        }
      }
    }
  }
})->purpose('cerrar periodos de solicitud de pre orden')
  ->dailyAt('19:00');

/**
 * command: retirar productos vencidos.
 * @param string $signature
 * @param \Closure $callback
 */
Artisan::command('products-expired:remove', function () {

  $this->info('Iniciando proceso de remoción de productos vencidos...');

  $stock_service = new StockService();
  $movement_vencimiento = StockMovement::MOVEMENT_TYPE_VENCIMIENTO();

  $day = now();
  $processed_count = 0;

  // log in de usuario sistema como responsable de operaciones automaticas
  $system_user = User::where('email', 'sistema@sistema.com')->first();
  if ($system_user) {
    Auth::login($system_user);
  }

  // servicio de auditoria
  $audit_service = new AuditService();

  try {

    $expired_stocks = $stock_service->getStocksToExpire();

    if ($expired_stocks->isEmpty()) {
      $this->info('No se encontraron productos vencidos.');
      return;
    }

    $this->info("Procesando {$expired_stocks->count()} productos vencidos...");

    // por cada stock
    foreach ($expired_stocks as $expired_stock) {

      // solo procesar si tiene cantidad restante
      if ($expired_stock->quantity_left > 0) {

        $original_quantity = $expired_stock->quantity_left;
        $original_stock_attributes = $expired_stock->getAttributes();

        DB::transaction(function () use ($expired_stock, $movement_vencimiento, $day, $original_quantity, $original_stock_attributes, $system_user, $audit_service) {
          
          // movimiento
          $stock_movement = StockMovement::create([
            'stock_id' => $expired_stock->id,
            'quantity' => -$original_quantity,
            'movement_type' => $movement_vencimiento,
            'registered_at' => $day,
          ]);

          $audit_service->auditModelCreated(
            model: $stock_movement,
            user: $system_user,
            additional_info: [
              'command' => 'products-expired:remove',
              'reason' => 'vencimiento_automatico',
            ]
          );
          
          // actualizacion de stock
          $expired_stock->update(['quantity_left' => 0]);

          $audit_service->auditModelUpdated(
            model: $expired_stock,
            original_attributes: $original_stock_attributes,
            user: $system_user,
            additional_info: [
              'command' => 'products-expired:remove',
              'reason' => 'vencimiento_automatico',
            ]
          );
          
        });
        
        $this->line("Stock ID {$expired_stock->id}: {$original_quantity} unidades vencidas");
        $processed_count++;
      }
    }

    $this->info("Se procesaron {$processed_count} productos vencidos exitosamente.");

    Log::info('Productos vencidos removidos', [
      'count' => $processed_count,
      'executed_at' => $day,
      'command' => 'products-expired:remove'
    ]);

    return 0;
  } catch (\Exception $e) {
    $this->error('Error al procesar productos vencidos: ' . $e->getMessage());

    Log::error('Error en command products-expired:remove', [
      'error' => $e->getMessage(),
      'trace' => $e->getTraceAsString(),
      'executed_at' => $day
    ]);

    return 1;
  }
})->purpose('quitar productos de un stock cuando alcanzaron su fecha de vencimiento')
  ->dailyAt('08:00');
