<?php

namespace App\Jobs;

use App\Models\PreOrderPeriod;
use App\Services\Supplier\PreOrderPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClosePreOrderPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   * @param PreOrderPeriod $period periodo de peticion de pre ordenes
   */
  public function __construct(public PreOrderPeriod $period) {}

  /**
   * Execute the job.
   */
  public function handle(PreOrderPeriodService $preorder_period_service): void
  {
    $this->period->period_status_id = $preorder_period_service->getStatusClosed();
    $this->period->save();
  }
}
