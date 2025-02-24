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

  public $pre_order_period_data = [
    'quotation_period_id' => null,
    'period_code' => 'TEST-001',
    'period_start_at' => '2024-01-01',
    'period_end_at' => '2024-12-31',
    'period_short_description' => 'Test Period',
    'period_status_id' => null
  ];

  public $user_data = [
    'name' => 'user',
    'email' => 'user@mail.com',
    'password' => '12345678',
  ];

  public  $address_data = [
    'street' => 'calle1',
    'number' => '123',
    'postal_code' => '3350',
    'city' => 'apotoles',
  ];

  public  $supplier_data = [
    'company_name' => 'arcor',
    'company_cuit' => '12345678912',
    'iva_condition' => 'monotributista',
    'phone_number' => '3755121447',
    'short_description' => 'description',
    'status_is_active' => true,
    'status_description' => 'dscription',
  ];

  public $pre_order_data = [
    'pre_order_period_id' => null,
    'supplier_id' => null,
    'pre_order_code' => 'TEST-001',
    'quotation_reference' => 'TEST-001',
    'status' => 'pendiente',
    'is_approved_by_supplier' => false,
    'is_approved_by_buyer' => false,
  ];

  /**
   * test crear periodo de pre orden individual
   * @return void
   */
  public function test_crear_periodo_de_pre_orden(): void
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->pre_order_period_data['period_status_id'] = $period_status->id;

    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->assertInstanceOf(PreOrderPeriod::class, $pre_order_period);
    $this->assertDatabaseHas('pre_order_periods', $this->pre_order_period_data);
  }

  /**
   * test crear periodo de pre orden a traves de un periodo presupuestario
   * @return void
   */
  public function test_crear_periodo_de_pre_orden_a_partir_de_un_periodo_presupuestario(): void
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data += ['period_status_id' => $period_status->id];
    $quotation_period = RequestForQuotationPeriod::create($this->period_data);

    $this->pre_order_period_data['quotation_period_id'] = $quotation_period->id;
    $this->pre_order_period_data['period_status_id'] = $period_status->id;

    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->assertInstanceOf(PreOrderPeriod::class, $pre_order_period);
    $this->assertDatabaseHas('pre_order_periods', $this->pre_order_period_data);
  }

  /**
   * test un periodo de pre orden puede pertenecer a un periodo de presupuestos
   * @return void
   */
  public function test_un_periodo_de_pre_orden_puede_pertenecer_a_un_periodo_presupuestario(): void
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data += ['period_status_id' => $period_status->id];
    $quotation_period = RequestForQuotationPeriod::create($this->period_data);

    $this->pre_order_period_data['quotation_period_id'] = $quotation_period->id;
    $this->pre_order_period_data['period_status_id'] = $period_status->id;

    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $this->assertInstanceOf(BelongsTo::class, $pre_order_period->quotation_period());
    $this->assertDatabaseHas('pre_order_periods', $this->pre_order_period_data);
  }

  /**
   * test un periodo de pre orden tiene muchas pre ordenes
   * @return void
   */
  public function test_un_periodo_de_pre_orden_tiene_muchas_pre_ordenes(): void
  {
    $period_status = PeriodStatus::create($this->period_status_data);
    $this->period_data += ['period_status_id' => $period_status->id];
    $quotation_period = RequestForQuotationPeriod::create($this->period_data);

    $this->pre_order_period_data['quotation_period_id'] = $quotation_period->id;
    $this->pre_order_period_data['period_status_id'] = $period_status->id;

    $pre_order_period = PreOrderPeriod::create($this->pre_order_period_data);

    $user = User::create($this->user_data);
    $addres = Address::create($this->address_data);

    $this->supplier_data += [
      'status_date' => formatDateTime(now(), 'Y-m-d'),
      'user_id' => $user->id,
      'address_id' => $addres->id
    ];
    $supplier = Supplier::create($this->supplier_data);

    $this->pre_order_data['pre_order_period_id'] = $pre_order_period->id;
    $this->pre_order_data['supplier_id'] = $supplier->id;
    $pre_order = PreOrder::create($this->pre_order_data);

    $this->assertInstanceOf(HasMany::class, $pre_order_period->pre_orders());
    $this->assertInstanceOf(BelongsTo::class, $pre_order->pre_order_period());
    $this->assertDatabaseHas('pre_orders', $this->pre_order_data);
  }
}
