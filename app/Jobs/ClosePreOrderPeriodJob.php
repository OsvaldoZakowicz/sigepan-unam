<?php

namespace App\Jobs;

use App\Models\PreOrder;
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
    // el periodo se cierra
    $this->period->period_status_id = $preorder_period_service->getStatusClosed();
    $this->period->save();

    // las pre ordenes no completadas (es decir, no respondidas) se rechazan
    $preorders_to_reject = $preorder_period_service->getPreOrdersToReject($this->period);

    foreach ($preorders_to_reject as $preorder) {
      $preorder->status = PreOrder::getRejectedStatus();
      $preorder->save();
    }
  }
}
