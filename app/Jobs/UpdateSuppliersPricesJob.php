<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateSuppliersPricesJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(public int $period_id) {}

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $quotation_period = RequestForQuotationPeriod::find($this->period_id);

    if (!$quotation_period) {
      return;
    }

    // presupuestos respondidos
    $quotations = $quotation_period->quotations()
      ->with(['provisions', 'packs', 'supplier'])
      ->where('is_completed', true)->get();

    // por cada presupuesto
    foreach ($quotations as $quotation) {
      // suministros
      foreach ($quotation->provisions as $quotation_provision) {
        if ((float)$quotation_provision->pivot->unit_price > 0 && $quotation_provision->pivot->has_stock) {
          $quotation->supplier->provisions()->updateExistingPivot(
            $quotation_provision->id,
            ['price' => $quotation_provision->pivot->unit_price]
          );
        }
      }
      // packs
      foreach ($quotation->packs as $quotation_pack) {
        if ((float)$quotation_pack->pivot->unit_price > 0 && $quotation_pack->pivot->has_stock) {
          $quotation->supplier->packs()->updateExistingPivot(
            $quotation_pack->id,
            ['price' => $quotation_pack->pivot->unit_price]
          );
        }
      }
    }
  }
}
