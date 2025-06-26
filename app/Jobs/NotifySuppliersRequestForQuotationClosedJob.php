<?php

namespace App\Jobs;

use App\Mail\RequestForQuotationClosed;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifySuppliersRequestForQuotationClosedJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   * @param int $period_id
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

    $suppliers_to_notify = $quotation_period->quotations->map(function ($quotation) {
      return [
        'supplier' => $quotation->supplier,
        'quotation' => $quotation
      ];
    });

    foreach ($suppliers_to_notify as $notify) {
      SendEmailJob::dispatch(
        $notify['supplier']->user->email,
        new RequestForQuotationClosed($notify['supplier'], $notify['quotation']),
      );
    }
  }
}
