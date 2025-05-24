<?php

namespace App\Livewire\Store;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\Sale\OrderService;
use App\Models\Sale;
use App\Models\DatoTienda;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class Orders extends Component
{
  use WithPagination;

  #[Url]
  public $search_order = ''; // codigo de orden

  #[Url]
  public $search_payment_status = ''; // estado del pago

  #[Url]
  public $search_order_status = ''; // estado de la orden

  #[Url]
  public $search_order_date = ''; // fecha de pedido

  // posibles estados del pago
  public $order_payment_status_pendiente = '';
  public $order_payment_status_aprobado = '';
  public $order_payment_status_rechazado = '';

  // posibles estados de la entrega del producto
  public $order_status_pendiente;
  public $order_status_entregado;
  public $order_status_cancelado;

  // tipos de ventas
  public $sale_type_web = '';
  public $sale_type_presencial = '';

  // modal de detalle
  public bool $show_details_modal = false;
  public $details_order = null;

  // modal de comprobante de pago
  public bool $show_payment_modal = false;
  public $payment_order = null;

  // modal de cancelar pedido
  public bool $show_cancel_modal = false;
  public $cancel_order = null;

  // datos de informacion sobre el pedido
  public $datos_tienda = null;

  /**
   * montar datos constantes
   * @return void
   */
  public function mount(): void
  {
    $this->order_payment_status_aprobado = Order::ORDER_PAYMENT_STATUS_APROBADO();
    $this->order_payment_status_pendiente = Order::ORDER_PAYMENT_STATUS_PENDIENTE();
    $this->order_payment_status_rechazado = Order::ORDER_PAYMENT_STATUS_RECHAZADO();

    $this->order_status_pendiente = OrderStatus::ORDER_STATUS_PENDIENTE();
    $this->order_status_entregado = OrderStatus::ORDER_STATUS_ENTREGADO();
    $this->order_status_cancelado = OrderStatus::ORDER_STATUS_CANCELADO();

    $this->sale_type_web = Sale::SALE_TYPE_WEB();
    $this->sale_type_presencial = Sale::SALE_TYPE_PRESENCIAL();

    // datos de informacion sobre el pedido
    $this->datos_tienda = DatoTienda::obtenerTodos();
  }

  /**
   * mostrar modal de detalles
   * @param int $id
   * @return void
   */
  public function showDetails(int $id): void
  {
    $this->details_order = Order::with(['products' => function ($query) {
      $query->withTrashed();
    }, 'sale'])->findOrFail($id);

    $this->show_details_modal = true;
  }

  /**
   * cerrar modal de detalles
   * @return void
   */
  public function closeDetails(): void
  {
    $this->show_details_modal = false;
    $this->details_order = null;
  }

  /**
   * mostrar modal de pago
   * @param int $id
   * @return void
   */
  public function showPayment(int $id): void
  {
    // orden con la venta
    $this->payment_order = Order::with('sale')->findOrFail($id);
    $this->show_payment_modal = true;
  }

  /**
   * cerrar modal de detalles
   * @return void
   */
  public function closePayment(): void
  {
    $this->show_payment_modal = false;
    $this->payment_order = null;
  }

  /**
   * redirigir para hacer el pago
   * @param int $id de orden
   */
  public function redirectToPay($id)
  {
    $order = Order::findOrFail($id);

    // redirigir a la vista del pago solo si no esta pagado
    if ($order->payment_status === $this->order_payment_status_pendiente && !$order->sale()->exists()) {
      // redirigir a vista individual del pedido + preferencia de pago
      return $this->redirectRoute('store-store-cart-index', ['id' => $order->id]);
    }

    session()->flash('operation-info', toastSuccessBody('pedido', 'ya fue pagado'));
    return;
  }

  /**
   * mostrar modal para cancelar el pedido
   * @param int $id de la orden a cancelar
   */
  public function showCancelOrderModal($id)
  {
    $this->show_cancel_modal = true;
    $this->cancel_order = $id;
  }

  /**
   * ocultar modal para cancelar el pedido
   */
  public function closeCancelOrderModal()
  {
    $this->show_cancel_modal = false;
    $this->cancel_order = null;
  }

  /**
   * cancelar pedido
   * @param int $id de orden
   */
  public function cancelOrder($id)
  {
    try {
      $orderService = new OrderService();
      $result = $orderService->cancelOrder($id);

      if ($result['success']) {

        // cerrar modal
        $this->closeCancelOrderModal();

        $this->dispatch('toast-event', toast_data: [
          'event_type'  =>  'success',
          'title_toast' =>  toastTitle('exitosa'),
          'descr_toast' =>  'El pedido fue cancelado.'
        ]);
      } else {

        // cerrar modal
        $this->closeCancelOrderModal();

        $this->dispatch('toast-event', toast_data: [
          'event_type'  =>  'info',
          'title_toast' =>  toastTitle('exitosa'),
          'descr_toast' =>  toastErrorBody('pedido', $result['message'])
        ]);
      }
    } catch (\Exception $e) {

      // cerrar modal
      $this->closeCancelOrderModal();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  toastErrorBody('pedido', 'Error al cancelar el pedido')
      ]);
    }
  }

  /**
   * buscar mis pedidos
   * @return mixed
   */
  public function searchOrders()
  {
    $orders = Order::with(['products' => function ($query) {
      $query->withTrashed();
    }, 'status', 'sale'])
      ->where('user_id', Auth::id())
      ->when($this->search_order, function ($query) {
        $query->where('order_code', 'like', '%' . $this->search_order . '%');
      })
      ->when($this->search_order_date, function ($query) {
        $query->whereDate('ordered_at', $this->search_order_date);
      })
      ->when($this->search_payment_status, function ($query) {
        $query->where('payment_status', $this->search_payment_status);
      })
      ->when($this->search_order_status, function ($query) {
          $query->where('order_status_id', $this->search_order_status);
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    return $orders;
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $orders = $this->searchOrders();
    return view('livewire.store.orders', compact('orders'));
  }
}
