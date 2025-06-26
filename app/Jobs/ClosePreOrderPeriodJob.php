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
   * @param int $period_id
   */
  public function __construct(public int $period_id) {}

  /**
   * Execute the job.
   */
  public function handle(PreOrderPeriodService $preorder_period_service): void
  {
    $preorder_period = PreOrderPeriod::find($this->period_id);

    if (!$preorder_period) {
      return;
    }

    // el periodo se cierra
    $preorder_period->period_status_id = $preorder_period_service->getStatusClosed();
    $preorder_period->save();

    // las pre ordenes no completadas (es decir, no respondidas) se rechazan
    $preorders_to_reject = $preorder_period_service->getPreOrdersToReject($preorder_period);

    foreach ($preorders_to_reject as $preorder) {
      $preorder->status = PreOrder::getRejectedStatus();
      $preorder->save();
    }
  }
}
