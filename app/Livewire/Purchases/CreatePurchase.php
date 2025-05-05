<?php

namespace App\Livewire\Purchases;

use App\Models\PreOrder;
use App\Models\Supplier;
use App\Services\Purchase\PurchaseService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class CreatePurchase extends Component
{
  // tipos que diferencian items de la orden de compras
  private $provision_type = 'provision';
  private $pack_type = 'pack';

  // preorden a partir de la cual puede registrarse una compra
  private ?PreOrder $preorder = null;
  // datos de la orden definitiva (desde la preorden)
  public $order_data;

  // datos para el formulario de compras
  public $with_preorder = false;
  public $formdt_supplier_id;
  public $formdt_purchase_date;
  public $formdt_order_code;
  public $formdt_order_date;
  public $formdt_purchase_items;
  public $suppliers;


  /**
   * montar datos
   * @param int|null $id de preorden
   * @return void
   */
  public function mount(PurchaseService $purchase_service, $id = null)
  {
    if ($id !== null) {

      $this->with_preorder = true;
      $this->preorder      = PreOrder::findOrFail($id);
      $this->order_data    = $purchase_service->getOrderData($this->preorder);

      $this->formdt_purchase_items = collect()
        ->merge($this->order_data['provisions'])
        ->merge($this->order_data['packs']);

    } else {

      $this->formdt_purchase_items = collect();

      $this->suppliers = Supplier::where('status_is_active', '1')
        ->orderBy('company_name')->get();

    }
  }

  /**
   * obtener tipo 'provision'
   * @return string
   */
  public function getProvisionType()
  {
    return $this->provision_type;
  }

  /**
   * obtener tipo 'pack'
   * @return string
   */
  public function getPackType()
  {
    return $this->pack_type;
  }

  /**
   * guardar compra
   */
  public function save(PurchaseService $purchase_service)
  {
    $validated = $this->validate([
        'formdt_supplier_id'    => ['required_if:with_preorder,false'],
        'formdt_purchase_date'  => ['required'],
        'formdt_purchase_items' => ['required'],
      ], [
        'formdt_supplier_id.required_if' => 'Debe elegir un proveedor para registrar la compra',
        'formdt_purchase_date.required'  => 'Debe indicar la fecha de la compra',
        'formdt_purchase_items.required' => 'Debe indicar al menos un suministro o pack adquirido'
      ]
    );

    try {

      $this->order_data['purchase_date'] = $validated['formdt_purchase_date'];
      $this->order_data['status'] = 'completada'; // todo: es necesario un estado?
      $purchase = $purchase_service->createPurchase($this->order_data, $this->formdt_purchase_items);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('compra', 'registrada'));
      $this->redirectRoute('purchases-purchases-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', $e->getMessage());
      $this->redirectRoute('purchases-purchases-index');

    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.purchases.create-purchase');
  }
}
