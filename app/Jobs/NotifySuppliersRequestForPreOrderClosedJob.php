<?php

namespace App\Jobs;

use App\Mail\RequestForPreOrderClosed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PreOrderPeriod;

class NotifySuppliersRequestForPreOrderClosedJob implements ShouldQueue
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
    $preorder_period = PreOrderPeriod::find($this->period_id);

    if (!$preorder_period) {
      return;
    }

    $suppliers_to_notify = $preorder_period->pre_orders->map(
      function ($pre_order) {
        return [
          'supplier' => $pre_order->supplier,
          'preorder' => $pre_order
        ];
      }
    );

    foreach ($suppliers_to_notify as $notify) {
      SendEmailJob::dispatch(
        $notify['supplier']->user->email,
        new RequestForPreOrderClosed($notify['supplier'], $notify['preorder'])
      );
    }
  }
}
