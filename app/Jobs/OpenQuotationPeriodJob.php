<?php

namespace App\Jobs;

use App\Models\Quotation;
use App\Models\RequestForQuotationPeriod;
use App\Models\Supplier;
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
   * crear la instancia del trabajo
   * @param RequestForQuotationPeriod $period periodo de peticion de presupuestos
   * @return void
   */
  public function __construct(public RequestForQuotationPeriod $period) {}

  /**
   * ejecutar el trabajo.
   * todo: manejo de errores?
   * @param QuotationPeriodService $quotation_period_service servicio para el periodo de presupuestos
   */
  public function handle(QuotationPeriodService $quotation_period_service): void
  {
    // estado: abierto
    $this->period->period_status_id = $quotation_period_service->getStatusOpen();
    $this->period->save();

    // * obtener todos los proveedores
    // con los suministros y/o que pueden proveer, que interesan al periodo

    // Obtenemos las provisiones del período con sus cantidades
    $period_provisions = $this->period->provisions()
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

    // Obtenemos los packs del período con sus cantidades
    $period_packs = $this->period->packs()
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

    // Consulta principal para obtener los proveedores con sus provisiones y packs
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
          'provisions'  => $supplier->provisions->map(function ($provision) use ($period_provisions){
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


    // por cada proveedor
    foreach ($all_suppliers as $supplier) {

      // crear un presupuesto a responder
      $quotation = Quotation::create([
        'quotation_code' => $quotation_period_service->generateUniqueQuotationCode(),
        'is_completed'   => false,
        'period_id'      => $this->period->id,
        'supplier_id'    => $supplier['supplier_id'],
      ]);

      // por cada suministro solicitado al proveedor
      foreach ($supplier['provisions'] as $provision) {
        // crear un "renglon" del presupuesto
        $quotation->provisions()->attach($provision['id'], [
          'has_stock'   => false,
          'quantity'    => $provision['quantity'],
          'unit_price'  => null,
          'total_price' => null,
        ]);
      }

      // por cada pack solicitado al proveedor
      foreach ($supplier['packs'] as $pack) {
        // crear un "renglon" del presupuesto
        $quotation->packs()->attach($pack['id'], [
          'has_stock'   => false,
          'quantity'    => $pack['quantity'],
          'unit_price'  => null,
          'total_price' => null,
        ]);
      }
    }
  }
}
