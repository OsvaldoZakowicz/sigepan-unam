<?php

namespace App\Jobs;

use App\Mail\NewRequestForPreOrderReceived;
use App\Models\PreOrderPeriod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifySuppliersRequestForPreOrderReceivedJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   * @param PreOrderPeriod $preorder_period
   */
  public function __construct(public PreOrderPeriod $preorder_period) {}

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $suppliers_to_notify = $this->preorder_period->pre_orders->map(
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
        new NewRequestForPreOrderReceived($notify['supplier'], $notify['preorder']),
      );
    }
  }
}
