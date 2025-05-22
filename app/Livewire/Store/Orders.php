<?php

namespace App\Livewire\Store;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Sale;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class Orders extends Component
{
  use WithPagination;

  // posibles estados del pago
  public $order_payment_status_pendiente = '';
  public $order_payment_status_aprobado = '';
  public $order_payment_status_rechazado = '';

  // posibles estados de la entrega del producto
  public $order_status_pendiente;
  public $order_status_entregado;
  public $order_status_cancelado;

  public bool $show_details_modal = false;
  public bool $show_payment_modal = false;
  public $details_order = null;
  public $payment_order = null;

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
  }

  /**
   * mostrar modal de detalles
   * @param int $id
   * @return void
   */
  public function showDetails(int $id): void
  {
    $this->details_order = Order::with(['products' => function($query) {
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
    $this->payment_order = Order::with(['products' => function($query) {
      $query->withTrashed();
    }, 'sale'])->findOrFail($id);

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

    if($order->payment_status === $this->order_payment_status_pendiente && !$order->sale()->exists())
    {
      // redirigir a vista individual del pedido + preferencia de pago
      return $this->redirectRoute('store-store-cart-index', ['id' => $order->id]);
    }

    session()->flash('operation-info', toastSuccessBody('pedido', 'ya fue pagado'));
    return;
  }

  /**
   * buscar mis pedidos
   * @return mixed
   */
  public function searchOrders()
  {
    $orders = Order::with(['products' => function($query) {
        $query->withTrashed();
      }, 'status', 'sale'])
      ->where('user_id', Auth::id())
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
