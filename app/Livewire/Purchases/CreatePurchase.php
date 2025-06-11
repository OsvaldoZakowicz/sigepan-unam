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
  public $with_preorder = false; // por defecto sin preorden
  public $suppliers;             // proveedores elegibles

  public $formdt_supplier_id;    // id provedor
  public $formdt_purchase_date;  // fecha de compra
  public $formdt_order_code;     // codigo de orden obtenida de la preorden
  public $formdt_order_date;     // fecha de orden obtenida de la preorden
  public $formdt_purchase_items; // items adquiridos (desde la preorden, o generados manualmente)

  // rango de fechas de compra
  public $date_max;
  public $date_min;

  // total de la compra (cuando los items son generados manualmente)
  public $total;


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

      // total del la compra
      $this->total = 0;
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
  public function getProvisionsAndPacksForSupplier()
  {

    // refrescar el componente de busqueda para el nuevo proveedor
    $this->dispatch('refresh-search', supplier_id: $this->formdt_supplier_id);

    // si ya habia seleccionado proveedor, y creado una lista de detalle, la misma debe vaciarse
    $this->formdt_purchase_items = collect();
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

      $unit_price     = (float) $pivot_data->price;
      $subtotal_price = (float) $unit_price * $quantity;

      $this->formdt_purchase_items->push([
        'item_type'      => $this->provision_type,
        'id'             => $provision->id,
        'name'           => $provision->provision_name,
        'description'    => $provision->provision_short_description,
        'trademark'      => $provision->trademark->provision_trademark_name,
        'type'           => $provision->type->provision_type_name,
        'unit_volume'    => $unit_volume,
        'item_count'     => $quantity,
        'total_volume'   => $total_volume,
        'unit_price'     => $unit_price,
        'subtotal_price' => $subtotal_price
      ]);

      // luego de agregar un item, calcular total
      $this->calculateTotal();
    } else {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'el suministro ya existe en la lista de detalle de compra!'
      ]);
    }
  }

  /**
   * * agregar suministros en packs a la lista de items de la compra
   * * el evento proviene de SearchProvision::class para Compras (Purchase)
   * @param Pack $pack un pack de suministros
   * @return void
   */
  #[On('add-pack')]
  public function addPack(Pack $pack): void
  {
    // verificar si el pack ya esta en la coleccion
    $exists = $this->formdt_purchase_items->contains(function ($item) use ($pack) {
      return $item['item_type'] === $this->pack_type && $item['id'] === $pack->id;
    });

    if (!$exists) {
      // calcular volumen unitario (del pack) y crear item con el formato requerido
      $unit_volume = convert_measure_value($pack->pack_quantity, $pack->provision->measure);

      // por defecto agregamos 1 unidad, luego se puede editar
      $quantity = 1;
      $total_volume = convert_measure_value(
        $pack->pack_quantity * $quantity,
        $pack->provision->measure
      );

      // obtener el precio desde la tabla pivote para el proveedor seleccionado
      $pivot_data = $pack->suppliers()
        ->where('supplier_id', $this->formdt_supplier_id)
        ->first()
        ->pivot;

      $unit_price     = (float) $pivot_data->price;
      $subtotal_price = (float) $unit_price * $quantity;

      $this->formdt_purchase_items->push([
        'item_type'      => $this->pack_type,
        'id'             => $pack->id,
        'name'           => $pack->pack_name,
        'description'    => $pack->provision->provision_short_description,
        'trademark'      => $pack->provision->trademark->provision_trademark_name,
        'type'           => $pack->provision->type->provision_type_name,
        'unit_volume'    => $unit_volume,
        'item_count'     => $quantity,
        'total_volume'   => $total_volume,
        'unit_price'     => $unit_price,
        'subtotal_price' => $subtotal_price,
      ]);

      // luego de agregar un item, calcular total
      $this->calculateTotal();
    } else {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'el pack ya existe en la lista de detalle de compra!'
      ]);
    }
  }

  /**
   * remover un suministro de la lista de precios
   * @param int $key clave del array de precios para el suministro
   * @return void
   */
  public function removeItem(int $key): void
  {
    $this->formdt_purchase_items->pull($key);
    $this->calculateTotal();

    // elemento eliminado de la lista
    $this->dispatch('toast-event', toast_data: [
      'event_type' => 'info',
      'title_toast' => toastTitle('', true),
      'descr_toast' => 'el item fue quitado de la lista de detalle de compra con éxito!'
    ]);
  }

  /**
   * vaciar la lista de suministros
   * @return void
   */
  public function resetList(): void
  {
    $this->formdt_purchase_items = collect();
    $this->total = 0;

    // lista vaciada correctamente
    $this->dispatch('toast-event', toast_data: [
      'event_type' => 'info',
      'title_toast' => toastTitle('', true),
      'descr_toast' => 'se vació la lista de detalle de compra con éxito!'
    ]);
  }

  /**
   * actualizar cantidad de un item y recalcular totales
   * @param int $key indice del item en la coleccion
   * @param $quantity nueva cantidad
   */
  public function updateItemQuantity(int $key, $quantity): void
  {
    // early return si NO es un numero entero
    // no actualiza cantidad ni subtotal
    // delega el manejo de errores en el campo item_count a las validaciones en save()
    if (!is_numeric($quantity) || !ctype_digit((string)$quantity)) {
      return;
    }

    // obtener el item
    $item = $this->formdt_purchase_items->get($key);

    // validar cantidad minima
    if ($quantity < 1) {
      $quantity = 1;
    }

    // actualizar cantidad
    $item['item_count'] = $quantity;

    // recalcular volumen total suministro
    if ($item['item_type'] === $this->provision_type) {
      $provision = Provision::find($item['id']);
      $item['total_volume'] = convert_measure_value(
        $provision->provision_quantity * $quantity,
        $provision->measure
      );
    }

    // recalcular volumen total pack
    if ($item['item_type'] === $this->pack_type) {
      $pack = Pack::find($item['id']);
      $item['total_volume'] = convert_measure_value(
        $pack->pack_quantity * $quantity,
        $pack->provision->measure
      );
    }

    // recalcular subtotal
    // $item['unit_price'] es float
    $subtotal = (float) $item['unit_price'] * $quantity;
    $item['subtotal_price'] = $subtotal; // float

    // actualizar item en la coleccion
    $this->formdt_purchase_items->put($key, $item);

    // recalcular el total
    $this->calculateTotal();
  }

  /**
   * dar formato de moneda al subtotal
   * @param int $key indice del item en la coleccion
   * @param $amount cantidad subtotal
   */
  public function formatItemSubtotal(int $key, $amount)
  {
    if (!is_numeric($amount) || (float) $amount <= 0) {

      // formato invalido, recalcular original
      $item = $this->formdt_purchase_items->get($key);
      $item['subtotal_price'] = (float) $item['unit_price'] * $item['item_count'];
    } else {

      // obtener el item y asignar valor
      $item = $this->formdt_purchase_items->get($key);
      $item['subtotal_price'] = $amount;
    }

    // actualizar item en la coleccion
    $this->formdt_purchase_items->put($key, $item);

    // recalcular el total
    $this->calculateTotal();
  }

  /**
   * calcular un total a mostrar al final de la tabla
   * @return void
   */
  public function calculateTotal(): void
  {
    $this->total = $this->formdt_purchase_items->reduce(function ($carry, $item) {
      return $carry + $item['subtotal_price'];
    }, 0);
  }

  /**
   * guardar compra
   *
   */
  public function save(PurchaseService $purchase_service)
  {
    $validated = $this->validate(
      [
        'formdt_supplier_id'    => ['required_if:with_preorder,false'],
        'formdt_purchase_date'  => ['required'],
        'formdt_purchase_items' => ['required'],
        'formdt_purchase_items.*.item_count'     => ['required', 'numeric', 'min:1', 'max:999', 'regex:/^\d{1,3}$/'],
        'formdt_purchase_items.*.subtotal_price' => ['required', 'numeric', 'min:0.01'],
      ],
      [
        'formdt_supplier_id.required_if' => 'Debe elegir un proveedor para registrar la compra.',
        'formdt_purchase_date.required'  => 'Debe indicar la fecha de la compra.',
        'formdt_purchase_items.required' => 'Debe indicar al menos un suministro o pack adquirido.',
        'formdt_purchase_items.*.item_count.required' => 'Debe indicar una cantidad comprada válida.',
        'formdt_purchase_items.*.item_count.numeric'  => 'La cantidad debe ser un número.',
        'formdt_purchase_items.*.item_count.min'      => 'La cantidad debe ser mayor a cero.',
        'formdt_purchase_items.*.item_count.max'      => 'La cantidad no puede exceder los 3 dígitos.',
        'formdt_purchase_items.*.item_count.regex'    => 'La cantidad debe ser un entero de máximo 3 dígitos.',
        'formdt_purchase_items.*.subtotal_price.required' => 'Debe indicar un subtotal',
        'formdt_purchase_items.*.subtotal_price.numeric'  => 'El subtotal debe ser un número válido.',
        'formdt_purchase_items.*.subtotal_price.min'      => 'El subtotal debe ser mayor a 0.',
      ]
    );

    try {

      // sanitizamos los datos justo antes de pasarlos al service
      $sanitized_items = collect($this->formdt_purchase_items)->map(function ($item) {
        return array_merge($item, [
          'item_count'     => (int) $item['item_count'],
          'unit_price'     => (float) $item['unit_price'],
          'subtotal_price' => (float) $item['subtotal_price'],
        ]);
      });

      if ($this->with_preorder) {

        $this->order_data['purchase_date'] = $validated['formdt_purchase_date'];
        $this->order_data['status']        = 'completada'; //? es necesario un estado?

        $purchase_service->createPurchase($this->order_data, $sanitized_items);
      } else {

        $new_purchase_data = [
          'supplier'      => Supplier::find($validated['formdt_supplier_id']),
          'purchase_date' => $validated['formdt_purchase_date'],
          'status'        => 'completada', //? es necesario un estado?
        ];

        $purchase_service->createPurchase($new_purchase_data, $sanitized_items);
      }

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
