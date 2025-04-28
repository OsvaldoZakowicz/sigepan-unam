<?php

namespace App\Livewire\Suppliers;

use App\Jobs\SendEmailJob;
use App\Mail\NewPurchaseOrderReceived;
use App\Models\Quotation;
use App\Models\PreOrder;
use App\Models\Provision;
use App\Models\Pack;
use App\Services\Pdf\PdfService;
use App\Services\Supplier\PreOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * * VISTA DE LA RESPUESTA DE PRE ORDEN PARA EL GERENTE
 */
class ShowPreOrderResponse extends Component
{
  // pre orden y presupuesto de referencia
  public PreOrder $preorder;
  public $preorder_details;
  public ?Quotation $quotation = null;

  // estados
  public string $status_pending;
  public string $status_approved;
  public string $status_rejected;

  // coleccion de suministros o packs
  public Collection $items;
  public float $total_price;

  // cuando el stock es false o cambia en algun item
  public Collection $alternative_items;
  public float $alternative_total_price;

  protected $PROVISION = 'provision';
  protected $PACK = 'pack';

  public $item_provision;
  public $item_pack;

  /**
   * boot de constantes
   * @return void
   */
  public function boot(): void
  {
    $this->status_pending = PreOrder::getPendingStatus();
    $this->status_approved = PreOrder::getApprovedStatus();
    $this->status_rejected = PreOrder::getRejectedStatus();
  }

  /**
   * montar datos
   * @param int $id id de la pre orden a visualizar
   * @return void
   */
  public function mount(int $id): void
  {
    $this->preorder = PreOrder::findOrFail($id);

    $this->preorder_details = json_decode($this->preorder->details, true);
    $this->quotation        = Quotation::where('quotation_code', $this->preorder->quotation_reference)->first();

    $this->item_provision = $this->PROVISION;
    $this->item_pack      = $this->PACK;

    $this->setProvisionsAndPacks();
    $this->getTotalPrice();
    $this->getAlternativeTotalPrice();
  }

  /**
   * preparar suministros y packs
   * de la pre orden para su visualizacion.
   * @return void
   */
  public function setProvisionsAndPacks(): void
  {
    $this->fill([
      'items'             => collect([]),
      'alternative_items' => collect([])
    ]);

    if ($this->preorder->provisions->count() > 0) {
      foreach ($this->preorder->provisions as $provision) {
        $this->addItem($provision);
      }
    }

    if ($this->preorder->packs->count() > 0) {
      foreach ($this->preorder->packs as $pack) {
        $this->addItem($pack);
      }
    }
  }

  /**
   * agregar un suministro o un pack al array de items e items alternativos.
   * La razon por la cual se hace esto es para obtener un diff cuando el stock
   * que el proveedor puede dar es 0 o una cantidad menor a la pedida.
   * * la coleccion alternativa siempre sera la indicada para la orden final
   * @param Provision | Pack $item es un suministro o pack
   * @return void
   */
  public function addItem(Provision|Pack $item): void
  {
    $type = ($item instanceof Provision) ? $this->PROVISION : $this->PACK;

    // Agregamos el item original
    $this->items->push([
      'item_id'                   =>  $item->id,
      'item_type'                 =>  $type,
      'item_object'               =>  $item,
      'item_has_stock'            =>  $item->pivot->has_stock,
      'item_quantity'             =>  $item->pivot->quantity,
      'item_alternative_quantity' =>  $item->pivot->alternative_quantity,
      'item_unit_price'           =>  $item->pivot->unit_price,
      'item_total_price'          =>  $item->pivot->total_price,
    ]);

    // en caso de que algun item tenga cantidad alternativa
    // Calculamos el precio total alternativo según las reglas
    $alternative_total_price = $item->pivot->has_stock
      ? $item->pivot->total_price
      : ($item->pivot->alternative_quantity > 0
        ? $item->pivot->alternative_quantity * $item->pivot->unit_price
        : 0);

    // creamos un item alternativo el cual:
    // sera una copia exacta del item inicial si stock = true
    // sera una copia con una cantidad alternativa y un precio total recalculado
    $this->alternative_items->push([
      'item_id'                   =>  $item->id,
      'item_type'                 =>  $type,
      'item_object'               =>  $item,
      'item_has_stock'            =>  $item->pivot->has_stock,
      'item_quantity'             =>  $item->pivot->quantity,
      'item_alternative_quantity' =>  $item->pivot->alternative_quantity,
      'item_unit_price'           =>  $item->pivot->unit_price,
      'item_total_price'          =>  $alternative_total_price,
    ]);
  }

  /**
   * calcular precio total
   * @return void
   */
  public function getTotalPrice(): void
  {
    $this->total_price = $this->items->reduce(function ($acc, $item) {
      return $acc + $item['item_total_price'];
    }, 0);
  }

  /**
   * calcular precio total alternativo
   * @return void
   */
  public function getAlternativeTotalPrice(): void
  {
    $this->alternative_total_price = $this->alternative_items->reduce(function ($acc, $item) {
      return $acc + $item['item_total_price'];
    }, 0);
  }

  /**
   * * aprobar pre orden y ordenar la compra
   */
  public function approveAndMakeOrder(PreOrderService $pos, PdfService $pdfs)
  {
    // datos para almacenar la orden en la BD como json
    $order_data = $pos->generateOrderData($this->preorder, $this->quotation, $this->alternative_items);

    // aprobar preorden, cambiar estado a aprobado, guardar en json los datos de la orden final
    $this->preorder->is_approved_by_buyer  = true;
    $this->preorder->status                = $this->status_approved;
    $this->preorder->order                 = json_encode($order_data);
    $this->preorder->is_sended_to_supplier = true;
    $this->preorder->save();

    $pdfs->generateOrderPDF($this->preorder, $order_data);

    // notificar al proveedor (el email adjuntara el pdf)
    SendEmailJob::dispatch(
      $this->preorder->supplier->user->email,
      new NewPurchaseOrderReceived(
        $this->preorder->supplier,
        $this->preorder
      )
    );

    // retornar a la vista anterior
    session()->flash('operation-success', toastSuccessBody('orden de compra', 'creada y enviada'));
    $this->redirectRoute('suppliers-preorders-show', $this->preorder->pre_order_period->id);
  }

  /**
   * * rechazar pre orden
   * el comprador no aprueba la pre orden
   * el estado de la pre orden cambia a rechazado
   */
  public function rejectPreOrder()
  {
    $this->preorder->update([
      'is_approved_by_buyer'  => false,
      'status'                => $this->status_rejected,
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.show-pre-order-response');
  }
}
