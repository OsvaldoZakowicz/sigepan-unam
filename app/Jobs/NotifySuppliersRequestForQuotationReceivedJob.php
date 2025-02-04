<?php

namespace App\Jobs;

use App\Mail\NewRequestForQuotationReceived;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifySuppliersRequestForQuotationReceivedJob implements ShouldQueue
{
  use Queueable;

  // periodo presupuestario
  protected RequestForQuotationPeriod $period;

  /**
   * Create a new job instance.
   * @param RequestFromQuotationPriod $period periodo presupuestario
   */
  public function __construct(RequestForQuotationPeriod $period)
  {
    $this->period = $period;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $suppliers_to_notify = $this->period->quotations->map(function ($quotation) {
      return [
        'supplier' => $quotation->supplier,
        'quotation' => $quotation
      ];
    });

    foreach ($suppliers_to_notify as $notify) {
      SendEmailJob::dispatch(
        $notify['supplier']->user->email,
        new NewRequestForQuotationReceived($notify['supplier'], $notify['quotation']),
      );
    }
  }
}
