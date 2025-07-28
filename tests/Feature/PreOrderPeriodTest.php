<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PeriodStatus;
use App\Models\PreOrderPeriod;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\PreOrder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class PreOrderPeriodTest extends TestCase
{
  use RefreshDatabase;

  public $period_quotation_status_data = [
    'status_name' => 'cerrado',
    'status_code' => 2,
    'status_short_description' => 'lorem ipsum'
  ];

  public $period_quotation_data = [
    'period_code' => 'periodo_1',
    'period_start_at' => '2024-11-26',
    'period_end_at' => '2024-11-28',
    'period_short_description' => 'lorem ipsum',
    'period_status_id' => null,
  ];

  public $period_preorder_status_data = [
    'status_name' => 'abierto',
    'status_code' => 1,
    'status_short_description' => 'lorem ipsum'
  ];

  public $period_preorder_data = [
    'quotation_period_id' => null,
    'period_code' => 'TEST-001',
    'period_start_at' => '2024-01-01',
    'period_end_at' => '2024-12-31',
    'period_short_description' => 'Test Period',
    'period_status_id' => null
  ];

  /**
   * @testCase TC001.
   * @purpose Crear un periodo de preorden.
   * @expectedResult Se crea un periodo de preordenes en el sistema.
   * @observations Sin periodo presupuestario previo.
   * @return void
   */
  public function test_crear_periodo_de_pre_orden(): void
  {
    $period_preorder_status = PeriodStatus::create($this->period_preorder_status_data);
    $this->period_preorder_data['period_status_id'] = $period_preorder_status->id;

    $pre_order_period = PreOrderPeriod::create($this->period_preorder_data);

    $this->assertInstanceOf(PreOrderPeriod::class, $pre_order_period);
    $this->assertDatabaseHas('pre_order_periods', $this->period_preorder_data);
  }

  /**
   * @testCase TC002.
   * @purpose Crear un periodo de preorden con periodo presupuestario previo.
   * @expectedResult Se crea un periodo de preordenes en el sistema.
   * @observations Asociado a un periodo presupuestario previo.
   * @return void
   */
  public function test_crear_periodo_de_pre_orden_a_partir_de_un_periodo_presupuestario(): void
  {
    $period_quotation_status = PeriodStatus::create($this->period_quotation_status_data);
    $this->period_quotation_data['period_status_id'] = $period_quotation_status->id;
    $quotation_period = RequestForQuotationPeriod::create($this->period_quotation_data);


    $period_preorder_status = PeriodStatus::create($this->period_preorder_status_data);
    $this->period_preorder_data['period_status_id'] = $period_preorder_status->id;
    $this->period_preorder_data['quotation_period_id'] = $quotation_period->id;
    $period_preorder = PreOrderPeriod::create($this->period_preorder_data);

    $this->assertInstanceOf(PreOrderPeriod::class, $period_preorder);
    $this->assertDatabaseHas('pre_order_periods', $this->period_preorder_data);
  }

  /**
   * @testCase TC003.
   * @purpose Un periodo de preorden tiene un periodo presupuestario asociado.
   * @expectedResult Se verifica que un periodo de preordenes tiene relacion con un periodo presupuestario en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_periodo_de_pre_orden_puede_pertenecer_a_un_periodo_presupuestario(): void
  {
    $period_preorder_status = PeriodStatus::create($this->period_preorder_status_data);
    $this->period_preorder_data['period_status_id'] = $period_preorder_status->id;

    $pre_order_period = PreOrderPeriod::create($this->period_preorder_data);

    $this->assertInstanceOf(BelongsTo::class, $pre_order_period->quotation_period());
    $this->assertDatabaseHas('pre_order_periods', $this->period_preorder_data);
  }

   /**
   * @testCase TC004.
   * @purpose Un periodo de preorden tiene preordenes asociadas.
   * @expectedResult Se verifica que un periodo de preordenes tiene relacion con preordenes en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_periodo_de_pre_orden_tiene_muchas_pre_ordenes(): void
  {
    $period_preorder_status = PeriodStatus::create($this->period_preorder_status_data);
    $this->period_preorder_data['period_status_id'] = $period_preorder_status->id;

    $pre_order_period = PreOrderPeriod::create($this->period_preorder_data);

    $this->assertInstanceOf(HasMany::class, $pre_order_period->pre_orders());
  }
}
