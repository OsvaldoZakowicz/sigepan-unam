<?php

namespace App\Services\Supplier;

use App\Models\PeriodStatus;

class BudgetPeriodService
{
  // todo: obtener de la base de datos
  public $status_scheduled_id = 1;
  public $status_open_id = 2;
  public $status_closed_id = 3;

  public function getStatusScheduled()
  {
    return $this->status_scheduled_id;
  }

  public function getStatusOpen()
  {
    return $this->status_open_id;
  }

  public function getStatusClosed()
  {
    return $this->status_closed_id;
  }
}
