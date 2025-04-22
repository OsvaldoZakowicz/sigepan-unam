<?php

namespace App\Jobs;

use App\Models\PreOrderPeriod;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\PreOrderPeriodService;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OpenPreOrderPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   * @param PreOrderPeriod $period es un periodo de pre ordenes
   */
  public function __construct(public PreOrderPeriod $preorder_period) {}

  /**
   * Execute the job.
   * @param PreOrderPeriodService $preorder_period_service
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function handle(
    PreOrderPeriodService $preorder_period_service,
    QuotationPeriodService $quotation_period_service
    ): void
  {
    // estado: abierto
    $this->preorder_period->period_status_id = $preorder_period_service->getStatusOpen();
    $this->preorder_period->save();

    //si el periodo de pre ordenes se crea a partir de un periodo presupuestario
    if ($this->preorder_period->quotation_period_id != null) {

      // obtener ranking de presupuestos,
      $quotation_period = RequestForQuotationPeriod::findOrFail($this->preorder_period->quotation_period_id);
      $quotations_ranking_data = $quotation_period_service->comparePricesBetweenQuotations($quotation_period->id);

      // crear pre ordenes
      $preorder_period_service->generatePreOrdersFromRanking($this->preorder_period->id, $quotations_ranking_data);

    } else {

      // crear pre ordenes desde datos base del periodo
      $preorder_period_service->generatePreOrdersFromScratch($this->preorder_period->id);
    }

  }
}
