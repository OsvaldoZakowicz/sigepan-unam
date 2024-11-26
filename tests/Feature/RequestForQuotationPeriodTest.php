<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PeriodStatus;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class RequestForQuotationPeriodTest extends TestCase
{
  use RefreshDatabase;

  public $period_status_data = [
    'status_name' => 'programado',
    'status_code' => 0,
    'status_short_description' => 'lorem ipsum'
  ];

  public $period_data = [
    'period_code' => 'periodo_1',
    'period_start_at' => '2024-11-26',
    'period_end_at' => '2024-11-28',
    'period_short_description' => 'lorem ipsum',
  ];

  //* crear un periodo de peticion de presupuestos
  public function test_crear_periodo_de_peticion()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data += ['period_status_id' => $period_status->id];
    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertDatabaseHas('request_for_quotation_periods', $this->period_data);
  }

  //* un periodo de peticion de presupuestos tiene un estado
  public function test_un_periodo_de_solicitud_tiene_un_estado()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data += ['period_status_id' => $period_status->id];
    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertInstanceOf(BelongsTo::class, $period->status());
  }
}
