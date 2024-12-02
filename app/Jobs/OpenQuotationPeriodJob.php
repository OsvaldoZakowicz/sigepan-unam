<?php

namespace App\Jobs;

use App\Models\Quotation;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OpenQuotationPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * * crear la instancia del trabajo
   */
  public function __construct(public RequestForQuotationPeriod $period) {}

  /**
   * * ejecutar el trabajo.
   * todo: comprobar la unicidad del codigo de quotation.
   */
  public function handle(QuotationPeriodService $quotation_period_service): void
  {
    // estado: abierto
    $this->period->period_status_id = $quotation_period_service->getStatusOpen();
    $this->period->save();

    // * obtener todos los proveedores, con los suministros
    // del periodo que pueden proveer
    $all_suppliers = $this->period->provisions
      ->flatMap(function ($provision) {
        return $provision->suppliers()
          ->where('status_is_active', true)
          ->get()
          ->map(function ($supplier) {
            return [
              'sup_id' => $supplier->id,
              'sup_name' => $supplier->company_name,
              'provisions' => $supplier->provisions()
                ->whereIn('provisions.id', $this->period->provisions()->pluck('provisions.id'))
                ->get()
            ];
          });
      })
      ->unique('sup_id')
      ->values()
      ->all();

    // por cada proveedor
    foreach ($all_suppliers as $key => $supplier) {
      // crear un presupuesto a responder
      $quotation = Quotation::create([
        'quotation_code'  => 'presupuesto_#' . $key . str_replace(':', '', now()->format('H:i:s')),
        'is_completed'    => false,
        'period_id'       => $this->period->id,
        'supplier_id'     => $supplier['sup_id'],
      ]);

      // por cada suministro solicitado al proveedor
      foreach ($supplier['provisions'] as $provision) {
        // crear un "renglon" del presupuesto
        $quotation->provisions()->attach($provision->id, ['has_stock' => false, 'price' => null]);
      }
    }

  }
}
