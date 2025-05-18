<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $statuses = [
      OrderStatus::ORDER_STATUS_PENDIENTE(),
      OrderStatus::ORDER_STATUS_ENTREGADO(),
      OrderStatus::ORDER_STATUS_CANCELADO(),
    ];

    foreach ($statuses as $status) {
      OrderStatus::create(['status' => $status]);
    }
  }
}
