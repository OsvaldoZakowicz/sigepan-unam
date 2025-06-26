<?php

namespace App\Jobs;

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseQuotationPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(public int $period_id) {}

  /**
   * Execute the job.
   */
  public function handle(QuotationPeriodService $quotation_period_service): void
  {
    $quotation_period = RequestForQuotationPeriod::find($this->period_id);

    if (!$quotation_period) {
      return;
    }

    // estado: cerrado
    $quotation_period->period_status_id = $quotation_period_service->getStatusClosed();
    $quotation_period->save();
  }
}
