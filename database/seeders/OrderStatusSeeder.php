<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // posibles estados
    $statuses = [
      OrderStatus::ORDER_STATUS_PENDIENTE_TEXT(),
      OrderStatus::ORDER_STATUS_CANCELADO_TEXT(),
      OrderStatus::ORDER_STATUS_ENTREGADO_TEXT(),
    ];

    // crear estados
    foreach ($statuses as $status) {
      OrderStatus::create(['status' => $status]);
    }
  }
}
