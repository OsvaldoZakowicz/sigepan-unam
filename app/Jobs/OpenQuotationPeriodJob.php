<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Quotation;
use App\Models\RequestForQuotationPeriod;
use App\Models\Supplier;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Audits\AuditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OpenQuotationPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * crear la instancia del trabajo
   * @param int $period_id
   * @return void
   */
  public function __construct(public int $period_id) {}

  /**
   * ejecutar el trabajo.
   * @param QuotationPeriodService $quotation_period_service servicio para el periodo de presupuestos
   */
  public function handle(QuotationPeriodService $quotation_period_service): void
  {
    Log::info('OpenQuotationPeriodJob started', ['period_id' => $this->period_id]);

    $quotation_period = RequestForQuotationPeriod::find($this->period_id);

    if (!$quotation_period) {
      Log::warning('Quotation period not found', ['period_id' => $this->period_id]);
      return;
    }

    // Obtener usuario del sistema ANTES de cualquier operación
    $system_user = User::where('email', 'sistema@sistema.com')->first();

    if (!$system_user) {
      Log::error('System user not found');
      return;
    }

    Log::info('System user found', ['user_id' => $system_user->id]);

    $audit_service = new AuditService();

    // Usar transacción para asegurar consistencia
    DB::transaction(function () use ($audit_service, $quotation_period, $quotation_period_service, $system_user) {

      // estado: abierto
      $quotation_period->period_status_id = $quotation_period_service->getStatusOpen();
      $quotation_period->save();

      // obtener todos los proveedores
      $period_provisions = $quotation_period->provisions()
        ->select('provisions.id')
        ->withPivot('quantity')
        ->get()
        ->mapWithKeys(function ($provision) {
          return [$provision->id => [
            'id' => $provision->id,
            'quantity' => $provision->pivot->quantity
          ]];
        })
        ->toArray();

      $period_packs = $quotation_period->packs()
        ->select('packs.id')
        ->withPivot('quantity')
        ->get()
        ->mapWithKeys(function ($pack) {
          return [$pack->id => [
            'id' => $pack->id,
            'quantity' => $pack->pivot->quantity
          ]];
        })
        ->toArray();

      $all_suppliers = Supplier::where('status_is_active', true)
        ->whereHas('provisions', function ($query) use ($period_provisions) {
          $query->whereIn('provisions.id', array_keys($period_provisions));
        })
        ->orWhereHas('packs', function ($query) use ($period_packs) {
          $query->whereIn('packs.id', array_keys($period_packs));
        })
        ->with(['provisions' => function ($query) use ($period_provisions) {
          $query->whereIn('provisions.id', array_keys($period_provisions));
        }, 'packs' => function ($query) use ($period_packs) {
          $query->whereIn('packs.id', array_keys($period_packs));
        }])
        ->get()
        ->map(function ($supplier) use ($period_provisions, $period_packs) {
          return [
            'supplier_id' => $supplier->id,
            'provisions'  => $supplier->provisions->map(function ($provision) use ($period_provisions) {
              return [
                'id'       => $provision->id,
                'name'     => $provision->provision_name,
                'quantity' => $period_provisions[$provision->id]['quantity'],
              ];
            })->values(),
            'packs' => $supplier->packs->map(function ($pack) use ($period_packs) {
              return [
                'id'       => $pack->id,
                'name'     => $pack->pack_name,
                'quantity' => $period_packs[$pack->id]['quantity'],
              ];
            })->values()
          ];
        })
        ->values()
        ->all();

      Log::info('Processing suppliers', ['count' => count($all_suppliers)]);

      // por cada proveedor
      foreach ($all_suppliers as $supplier) {

        Log::info('Creating quotation for supplier', ['supplier_id' => $supplier['supplier_id']]);

        // crear un presupuesto a responder
        $quotation = Quotation::create([
          'quotation_code' => $quotation_period_service->generateUniqueQuotationCode(),
          'is_completed'   => false,
          'period_id'      => $quotation_period->id,
          'supplier_id'    => $supplier['supplier_id'],
        ]);

        Log::info('Quotation created', [
          'quotation_id' => $quotation->id,
          'quotation_code' => $quotation->quotation_code
        ]);

        // IMPORTANTE: Refrescar el modelo desde la base de datos
        // para asegurar que tiene todos los datos necesarios
        $quotation = $quotation->fresh();

        // auditar creacion del presupuesto
        $audit_result = $audit_service->auditModelCreated(
          model: $quotation,
          user: $system_user,
          additional_info: [
            'job' => 'open-quotation-periods-job',
            'reason' => 'apertura-periodo-presupuestario',
          ]
        );

        if ($audit_result) {
          Log::info('Audit created successfully', [
            'audit_id' => $audit_result->id,
            'quotation_id' => $quotation->id
          ]);
        } else {
          Log::error('Failed to create audit', [
            'quotation_id' => $quotation->id
          ]);
        }

        // por cada suministro solicitado al proveedor
        foreach ($supplier['provisions'] as $provision) {
          $quotation->provisions()->attach($provision['id'], [
            'has_stock'   => false,
            'quantity'    => $provision['quantity'],
            'unit_price'  => 0,
            'total_price' => 0,
          ]);
        }

        // por cada pack solicitado al proveedor
        foreach ($supplier['packs'] as $pack) {
          $quotation->packs()->attach($pack['id'], [
            'has_stock'   => false,
            'quantity'    => $pack['quantity'],
            'unit_price'  => 0,
            'total_price' => 0,
          ]);
        }
      }
    });

    Log::info('OpenQuotationPeriodJob completed successfully', ['period_id' => $this->period_id]);
  }
}
