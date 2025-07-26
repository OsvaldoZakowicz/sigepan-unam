<?php

namespace App\Jobs;

use App\Models\PreOrderPeriod;
use App\Models\RequestForQuotationPeriod;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\Supplier;
use App\Models\PreOrder;
use App\Models\PreOrderPack;
use App\Models\PreOrderProvision;
use App\Services\Supplier\PreOrderPeriodService;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\Audits\AuditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OpenPreOrderPeriodJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   * @param int $period_id id de periodo de preordenes
   */
  public function __construct(public int $period_id) {}

  /**
   * Execute the job.
   * @param PreOrderPeriodService $preorder_period_service
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function handle(
    PreOrderPeriodService $preorder_period_service,
    QuotationPeriodService $quotation_period_service
    ): void
  {
    $preorder_period = PreOrderPeriod::find($this->period_id);

    // verificar existencia del periodo
    if (!$preorder_period) {
      Log::error('Preorder period not found');
      return;
    }

    // obtener usuario del sistema ANTES de cualquier operación
    $system_user = User::where('email', 'sistema@sistema.com')->first();

    if (!$system_user) {
      Log::error('System user not found');
      return;
    }

    //si el periodo de pre ordenes se crea a partir de un periodo presupuestario
    if ($preorder_period->quotation_period_id != null) {

      // * crear preordenes a partir del ranking
      DB::transaction(function () use ($preorder_period_service, $quotation_period_service, $system_user, $preorder_period) {

        $audit_service = new AuditService();
        
        // estado: abierto
        $preorder_period->period_status_id = $preorder_period_service->getStatusOpen();
        $preorder_period->save();

        // obtener ranking de presupuestos,
        $quotation_period = RequestForQuotationPeriod::findOrFail($preorder_period->quotation_period_id);
        $quotations_ranking_data = $quotation_period_service->comparePricesBetweenQuotations($quotation_period->id);

        // Agrupar los mejores precios por proveedor
        $best_prices_by_supplier = $preorder_period_service->groupBestPricesBySupplier($quotations_ranking_data);

        $generated_pre_orders = [];

        // Crear pre-ordenes para cada proveedor
        foreach ($best_prices_by_supplier as $supplier_id => $items) {

          $pre_order = PreOrder::create([
            'pre_order_period_id'     => $preorder_period->id,
            'supplier_id'             => $supplier_id,
            'pre_order_code'          => $preorder_period_service->generateUniquePreorderCode(),
            'quotation_reference'     => $items[0]['quotation_id'],
            'status'                  => PreOrder::getPendingStatus(),
            'is_completed'            => false,
            'is_approved_by_supplier' => false,
            'is_approved_by_buyer'    => false,
            'details'                 => null
          ]);

          $pre_order = $pre_order->fresh();

          // auditar pre orden
          $audit_result = $audit_service->auditModelCreated(
            model: $pre_order,
            user: $system_user,
            additional_info: [
              'job' => 'open-preorders-periods-job',
              'reason' => 'apertura-periodo-de-preordenes',
            ]
          );

          if ($audit_result) {
            Log::info('Audit created successfully', [
              'audit_id' => $audit_result->id,
              'preorder_id' => $pre_order->id
            ]);
          } else {
            Log::error('Failed to create audit', [
              'preorder_id' => $pre_order->id
            ]);
          }

          // Crear items para la pre-orden
          foreach ($items as $item) {
            if ($item['type'] === 'provision') {

              // completa la tabla pre_order_provision
              $preorder_provision = PreOrderProvision::create([
                'pre_order_id'          => $pre_order->id,
                'provision_id'          => $item['id'],
                'has_stock'             => true,
                'quantity'              => $item['quantity'],
                'alternative_quantity'  => 0,
                'unit_price'            => $item['unit_price'],
                'total_price'           => $item['total_price']
              ]);

              $preorder_provision = $preorder_provision->fresh();

              // auditar la creacion del modelo pivot
              $audit_service->auditModelCreated(
                model: $preorder_provision,
                user: $system_user,
                additional_info: [
                  'job' => 'open-quotation-periods-job',
                  'reason' => 'creacion-suministro-preorden',
                  'preorder_id' => $pre_order->id,
                  'provision_id' => $item['id'],
                ]
              );

              Log::info('PreOrderProvision created and audited', [
                'preorder_provision_id' => $preorder_provision->id,
                'preorder_id' => $pre_order->id,
                  'provision_id' => $item['id'],
              ]);
            }

            if ($item['type'] === 'pack') {

              // auditar la creacion del modelo pivot
              $preorder_pack = PreOrderPack::create([
                'pre_order_id'          => $pre_order->id,
                'pack_id'               => $item['id'],
                'has_stock'             => true,
                'quantity'              => $item['quantity'],
                'alternative_quantity'  => 0,
                'unit_price'            => $item['unit_price'],
                'total_price'           => $item['total_price']
              ]);

              $preorder_pack = $preorder_pack->fresh();

              // auditar la creacion del modelo pivot
              $audit_service->auditModelCreated(
                model: $preorder_pack,
                user: $system_user,
                additional_info: [
                  'job' => 'open-quotation-periods-job',
                  'reason' => 'creacion-pack-preorden',
                  'preorder_id' => $pre_order->id,
                  'pack_id' => $item['id'],
                ]
              );

              Log::info('PreOrderPack created and audited', [
                'preorder_pack_id' => $preorder_pack->id,
                'preorder_id' => $pre_order->id,
                'pack_id' => $item['id'],
              ]);
            }
          }

          $generated_pre_orders[] = $pre_order->load(['packs', 'provisions']);
        }

      });

    } else {

      // * crear pre ordenes desde datos base del periodo
      DB::transaction(function () use ($preorder_period_service, $system_user, $preorder_period) {

        $audit_service = new AuditService();

        // estado: abierto
        $preorder_period->period_status_id = $preorder_period_service->getStatusOpen();
        $preorder_period->save();

        // obtener suministros y packs del periodo (JSON)
        $provisions_and_packs = $preorder_period_service->getProvisionAndPacksData($preorder_period->id);

        // agrupar suministros y packs por proveedor
        $suppliers_items = [];

        // Procesar suministros
        foreach ($provisions_and_packs['provisions'] as $provision_data) {
          $supplier = $provision_data['supplier'];
          $supplier_key = 'supplier' . $supplier->id;

          // Inicializar el array para el proveedor si no existe
          if (!isset($suppliers_items[$supplier_key])) {
            $suppliers_items[$supplier_key] = [
              'supplier' => $supplier,
              'provisions' => [],
              'packs' => []
            ];
          }

          // Agregar el suministro al array del proveedor
          $suppliers_items[$supplier_key]['provisions'][] = [
            'provision' => $provision_data['provision'],
            'quantity' => $provision_data['quantity']
          ];
        }

        // Procesar packs
        foreach ($provisions_and_packs['packs'] as $pack_data) {
          $supplier = $pack_data['supplier'];
          $supplier_key = 'supplier' . $supplier->id;

          // Inicializar el array para el proveedor si no existe
          if (!isset($suppliers_items[$supplier_key])) {
            $suppliers_items[$supplier_key] = [
              'supplier' => $supplier,
              'provisions' => [],
              'packs' => []
            ];
          }

          // Agregar el pack al array del proveedor
          $suppliers_items[$supplier_key]['packs'][] = [
            'pack' => $pack_data['pack'],
            'quantity' => $pack_data['quantity']
          ];
        }

        // pre ordenes generadas
        $generated_pre_orders = [];

        // crear pre ordenes por proveedor y crear items de pre orden
        foreach ($suppliers_items as $supplier_item) {

          // pre orden
          $pre_order = PreOrder::create([
            'pre_order_period_id'     => $preorder_period->id,
            'supplier_id'             => $supplier_item['supplier']->id,
            'pre_order_code'          => $preorder_period_service->generateUniquePreorderCode(),
            'quotation_reference'     => null,
            'status'                  => PreOrder::getPendingStatus(),
            'is_completed'            => false,
            'is_approved_by_supplier' => false,
            'is_approved_by_buyer'    => false,
            'details'                 => null
          ]);

          $pre_order = $pre_order->fresh();

          // auditar pre orden
          $audit_result = $audit_service->auditModelCreated(
            model: $pre_order,
            user: $system_user,
            additional_info: [
              'job' => 'open-preorders-periods-job',
              'reason' => 'apertura-periodo-de-preordenes',
            ]
          );

          if ($audit_result) {
            Log::info('Audit created successfully', [
              'audit_id' => $audit_result->id,
              'preorder_id' => $pre_order->id
            ]);
          } else {
            Log::error('Failed to create audit', [
              'preorder_id' => $pre_order->id
            ]);
          }

          // suministros de la pre orden
          foreach ($supplier_item['provisions'] as $item_provision) {

            // Obtener el precio del suministro para este proveedor
            $provision_price = $supplier_item['supplier']->provisions()
              ->where('provisions.id', $item_provision['provision']->id)
              ->first()
              ->pivot
              ->price ?? 0;

            $item_data = [
              'type'        =>  'provision',
              'id'          =>  $item_provision['provision']->id,
              'quantity'    =>  $item_provision['quantity'],
              'unit_price'  =>  $provision_price,
              'total_price' =>  $provision_price * $item_provision['quantity']

            ];

            // completa la tabla pre_order_provision
            $preorder_provision = PreOrderProvision::create([
              'pre_order_id'          => $pre_order->id,
              'provision_id'          => $item_data['id'],
              'has_stock'             => true,
              'quantity'              => $item_data['quantity'],
              'alternative_quantity'  => 0,
              'unit_price'            => $item_data['unit_price'],
              'total_price'           => $item_data['total_price']
            ]);

            $preorder_provision = $preorder_provision->fresh();

            // auditar la creacion del modelo pivot
            $audit_service->auditModelCreated(
              model: $preorder_provision,
              user: $system_user,
              additional_info: [
                'job' => 'open-quotation-periods-job',
                'reason' => 'creacion-suministro-preorden',
                'preorder_id' => $pre_order->id,
                'provision_id' => $item_data['id'],
              ]
            );

            Log::info('PreOrderProvision created and audited', [
              'preorder_provision_id' => $preorder_provision->id,
              'preorder_id' => $pre_order->id,
                'provision_id' => $item_data['id'],
            ]);
          }

          // packs de la pre orden
          foreach ($supplier_item['packs'] as $item_pack) {

            // Obtener el precio del pack para este proveedor
            $pack_price = $supplier_item['supplier']->packs()
              ->where('packs.id', $item_pack['pack']->id)
              ->first()
              ->pivot
              ->price ?? 0;

            $item_data = [
              'type'        =>  'pack',
              'id'          =>  $item_pack['pack']->id,
              'quantity'    =>  $item_pack['quantity'],
              'unit_price'  =>  $pack_price,
              'total_price' =>  $pack_price * $item_pack['quantity']
            ];

            // auditar la creacion del modelo pivot
            $preorder_pack = PreOrderPack::create([
              'pre_order_id'          => $pre_order->id,
              'pack_id'               => $item_data['id'],
              'has_stock'             => true,
              'quantity'              => $item_data['quantity'],
              'alternative_quantity'  => 0,
              'unit_price'            => $item_data['unit_price'],
              'total_price'           => $item_data['total_price']
            ]);

            $preorder_pack = $preorder_pack->fresh();

            // auditar la creacion del modelo pivot
            $audit_service->auditModelCreated(
              model: $preorder_pack,
              user: $system_user,
              additional_info: [
                'job' => 'open-quotation-periods-job',
                'reason' => 'creacion-pack-preorden',
                'preorder_id' => $pre_order->id,
                'pack_id' => $item_data['id'],
              ]
            );

            Log::info('PreOrderPack created and audited', [
              'preorder_pack_id' => $preorder_pack->id,
              'preorder_id' => $pre_order->id,
              'pack_id' => $item_data['id'],
            ]);
          }

          // Agregar la pre-orden con sus relaciones cargadas al array
          $generated_pre_orders[] = $pre_order->load(['packs', 'provisions']);
        }

      });

    }
  }
}
