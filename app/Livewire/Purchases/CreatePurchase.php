<?php

namespace App\Livewire\Purchases;

use App\Models\PreOrder;
use App\Models\Supplier;
use App\Services\Purchase\PurchaseService;
use App\Models\Provision;
use App\Models\Pack;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\View\View;
use Livewire\Component;

class CreatePurchase extends Component
{
  use WithPagination;

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

  // rango de fechas de compra
  public $date_max;
  public $date_min;


  /**
   * montar datos
   * si no se recibe un id de preorden, se procede a crear una compra desde cero
   * @param int|null $id de preorden
   * @return void
   */
  public function mount(PurchaseService $purchase_service, $id = null)
  {
    // fecha maxima de registro de compra, hasta el dia actual
    $this->date_max = now()->format('Y-m-d');

    // fecha minima de registro de compra, un mes antes
    $this->date_min = now()->subDays(30)->format('Y-m-d');

    if ($id !== null) {

      // * compra desde preorden

      $this->with_preorder     = true;
      $this->preorder          = PreOrder::findOrFail($id);
      $this->order_data        = $purchase_service->getOrderData($this->preorder);

      // fecha minima de registro de compra, con preorden, la misma fecha que se ordeno
      $this->date_min = $this->order_data['order_date'];

      $this->formdt_purchase_items = collect()
        ->merge($this->order_data['provisions'])
        ->merge($this->order_data['packs']);
    } else {

      // * compra desde cero

      // coleccion de suministros que se registraran en la compra, para detalle de compra
      $this->formdt_purchase_items = collect();

      // proveedores disponibles para registrar una compra
      $this->suppliers = $purchase_service->getActiveSuppliers();
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
   * para el proveedor elegido en el select, obtener todos los suministros y packs que vende
   * * cuando no hay preorden asociada.
   * refresca el cuadro de busqueda
   */
  public function getProvisionsAndPacksForSupplier(int $id)
  {
    // buscar el proveedor elegido
    $selected_supplier = Supplier::with(['provisions', 'packs'])
      ->where('id', $id)->first();

    // refrescar el componente de busqueda para el nuevo proveedor
    $this->dispatch('refresh-search', supplier_id: $this->formdt_supplier_id);
  }

  /**
   * * agregar suministros a la lista de items de la compra
   * * el evento proviene de SearchProvision::class para Compras (Purchase)
   * @param Provision $provision un suministro
   * @return void
   */
  #[On('add-provision')]
  public function addProvision(Provision $provision): void
  {
    // verificar si el suministro ya esta en la coleccion
    $exists = $this->formdt_purchase_items->contains(function ($item) use ($provision) {
      return $item['item_type'] === $this->provision_type && $item['id'] === $provision->id;
    });

    if (!$exists) {
      // calcular volumen unitario y crear item con el formato requerido
      $unit_volume = convert_measure_value($provision->provision_quantity, $provision->measure);

      // por defecto agregamos 1 unidad, luego se puede editar
      $quantity = 1;
      $total_volume = convert_measure_value(
        $provision->provision_quantity * $quantity,
        $provision->measure
      );

      // obtener el precio desde la tabla pivote para el proveedor seleccionado
      $pivot_data = $provision->suppliers()
        ->where('supplier_id', $this->formdt_supplier_id)
        ->first()
        ->pivot;

      $unit_price = $pivot_data->price;
      $subtotal_price = $unit_price * $quantity;

      $this->formdt_purchase_items->push([
        'item_type'      => $this->provision_type,
        'id'             => $provision->id,
        'name'           => $provision->provision_name,
        'description'    => $provision->description,
        'trademark'      => $provision->trademark->provision_trademark_name,
        'type'           => $provision->type->provision_type_name,
        'unit_volume'    => $unit_volume,
        'item_count'     => $quantity,
        'total_volume'   => $total_volume,
        'unit_price'     => $unit_price,
        'subtotal_price' => $subtotal_price
      ]);
    }
  }

  /**
   * Actualizar cantidad de un item y recalcular totales
   * @param int $key Índice del item en la colección
   * @param int $quantity Nueva cantidad
   */
  public function updateItemQuantity(int $key, int $quantity): void
  {
    // Obtener el item
    $item = $this->formdt_purchase_items->get($key);

    // Validar cantidad mínima
    if ($quantity < 1) {
      $quantity = 1;
    }

    // Actualizar cantidad
    $item['item_count'] = $quantity;

    // Recalcular volumen total
    if ($item['item_type'] === $this->provision_type) {
      $provision = Provision::find($item['id']);
      $item['total_volume'] = convert_measure_value(
        $provision->provision_quantity * $quantity,
        $provision->measure
      );
    }

    // Recalcular subtotal
    $item['subtotal_price'] = $item['unit_price'] * $quantity;

    // Actualizar item en la colección
    $this->formdt_purchase_items->put($key, $item);
  }

  /**
   * guardar compra
   */
  public function save(PurchaseService $purchase_service)
  {
    $validated = $this->validate(
      [
        'formdt_supplier_id'    => ['required_if:with_preorder,false'],
        'formdt_purchase_date'  => ['required'],
        'formdt_purchase_items' => ['required'],
      ],
      [
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
