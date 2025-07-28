<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PeriodStatus;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    'period_status_id' => null,
  ];

  /**
   * @testCase TC001.
   * @purpose Crear un periodo de peticion de presupuestos.
   * @expectedResult Se crea un periodo de peticion de presupuestos en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_crear_periodo_de_peticion_de_presupuestos()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data['period_status_id'] = $period_status->id;

    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertDatabaseHas('request_for_quotation_periods', $this->period_data);
    $this->assertInstanceOf(RequestForQuotationPeriod::class, $period);
  }


  /**
   * @testCase TC002.
   * @purpose Un periodo de peticion de presupuestos tiene un estado.
   * @expectedResult Se verifica que un periodo de peticion de presupuestos tiene un estado en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_periodo_de_peticion_de_presupuestos_tiene_un_estado()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data['period_status_id'] = $period_status->id;

    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertInstanceOf(BelongsTo::class, $period->status());
  }

  /**
   * @testCase TC003.
   * @purpose Un periodo de peticion de presupuestos tiene suministros y packs.
   * @expectedResult Se verifica que un periodo de peticion de presupuestos tiene suministros y packs en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_periodo_de_peticion_de_presupuestos_tiene_suministros_y_packs()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data['period_status_id'] = $period_status->id;

    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertInstanceOf(BelongsToMany::class, $period->provisions());
    $this->assertInstanceOf(BelongsToMany::class, $period->packs());
  }

  /**
   * @testCase TC004.
   * @purpose Un periodo de peticion de presupuestos tiene presupuestos asociados.
   * @expectedResult Se verifica que un periodo de peticion de presupuestos tiene presupuestos en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_periodo_de_peticion_de_presupuestos_tiene_presupuestos()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data['period_status_id'] = $period_status->id;

    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertInstanceOf(HasMany::class, $period->quotations());
  }

  /**
   * @testCase TC005.
   * @purpose Un periodo de peticion de presupuestos puede usarse como partida para un periodo de pre ordenes de compra.
   * @expectedResult Se verifica que un periodo de peticion de presupuestos puede usarse como partida para un periodo de pre ordenes de compra en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_periodo_de_peticion_de_presupuestos_puede_usarse_en_periodo_de_preordenes()
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data['period_status_id'] = $period_status->id;

    $period = RequestForQuotationPeriod::create($this->period_data);

    $this->assertInstanceOf(HasOne::class, $period->preorder_period());
  }
}
