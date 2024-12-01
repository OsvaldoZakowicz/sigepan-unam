<?php

namespace App\Jobs;

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class OpenQuotationPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(public RequestForQuotationPeriod $period) {}

  /**
   * Execute the job.
   */
  public function handle(QuotationPeriodService $quotation_period_service): void
  {
    // estado: abierto
    $this->period->period_status_id = $quotation_period_service->getStatusOpen();
    $this->period->save();

  }
}
