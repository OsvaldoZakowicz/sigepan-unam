<?php

namespace App\Livewire\Sales;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\Sale\OrderService;
use App\Models\Sale;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListOrders extends Component
{
  use WithPagination;

  #[Url]
  public $search_order = ''; // codigo de orden

  #[Url]
  public $search_payment_status = ''; // estado del pago

  #[Url]
  public $search_order_status = ''; // estado de la orden

  #[Url]
  public $search_start_at = ''; // fecha de pedido desde

  #[Url]
  public $search_end_at = ''; // fecha de pedido hasta

  // posibles estados del pago
  public $order_payment_status_pendiente = '';
  public $order_payment_status_aprobado = '';
  public $order_payment_status_rechazado = '';

  // posibles estados de la entrega del producto
  public $order_status_pendiente;
  public $order_status_entregado;
  public $order_status_cancelado;

  // modal de detalle
  public bool $show_details_modal = false;
  public $details_order = null;
  public $details_user = null;

  // modal de comprobante de pago
  public bool $show_payment_modal = false;
  public $payment_order = null;

  // tipos de ventas
  public $sale_type_web = '';
  public $sale_type_presencial = ''; // provisorio

  // modal de cancelar pedido
  public bool $show_cancel_modal = false;
  public $cancel_order = null;

  // modal marcar entrega del producto
  public bool $show_entrega_modal = false;
  public $order_to_entrega = null;

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
  }

  /**
   * Mostrar modal de detalles
   * @param int $id
   * @return void
   */
  public function showDetails(int $id): void
  {
    $this->details_order = Order::with([
      'products' => function ($query) {
        $query->withTrashed();
      },
      'sale',
      'user' => function ($query) {
        $query->withTrashed()
          ->with([
            'profile' => function ($q) {
              $q->withTrashed()
                ->with([
                  'address' => function ($q) {
                    $q->withTrashed();
                  }
                ]);
            }
          ]);
      }
    ])
      ->findOrFail($id);


    if ($this->details_order->user) {
      $usr = $this->details_order->user;
      $username = $usr->name;
      $email = $usr->email;
    } else {
      $username = '-';
      $email = '-';
    }

    if ($this->details_order->user->profile) {
      $pr = $this->details_order->user->profile;
      $fullname = $pr->first_name . ', ' . $pr->last_name;
      $dni = $pr->dni;
      $contact = $pr->phone_number;
    } else {
      $fullname = '-';
      $contact = '-';
      $dni = '-';
    }

    if ($this->details_order->user->profile->address) {
      $adr = $this->details_order->user->profile->address;
      $full_address = $adr->street . ', numero ' . $adr->number . ', ciudad: '
        . $adr->city . ', CP' . $adr->postal_code;
    } else {
      $full_address = '-';
    }

    $this->details_user = [
      'username'        =>  $username,
      'email'           =>  $email,
      'full_name'       =>  $fullname,
      'contact'         =>  $contact,
      'dni'             =>  $dni,
      'full_address'    =>  $full_address,
      'account_status'  =>  $this->details_order->user->trashed() ? 'cuenta borrada' : 'usuario activo'
    ];

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
          'title_toast' =>  toastTitle('', true),
          'descr_toast' =>  $result['message']
        ]);
      }
    } catch (\Exception $e) {

      // cerrar modal
      $this->closeCancelOrderModal();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  $result['message']
      ]);
    }
  }

  /**
   * mostrar modal para marcar la entrega del pedido
   * @param int $id de la orden a cancelar
   */
  public function showEntregarOrderModal($id)
  {
    $this->show_entrega_modal = true;
    $this->order_to_entrega = $id;
  }

  /**
   * ocultar modal para cancelar el pedido
   */
  public function closeEntregarOrderModal()
  {
    $this->show_entrega_modal = false;
    $this->order_to_entrega = null;
  }

  /**
   * entregar los pedidos de la orden
   * @param int $id de la orden a entregar
   */
  public function entregaOrder($id)
  {
    try {
      $orderService = new OrderService();
      $result = $orderService->entregaOrder($id);

      if ($result['success']) {

        // cerrar modal
        $this->closeEntregarOrderModal();

        $this->dispatch('toast-event', toast_data: [
          'event_type'  =>  'success',
          'title_toast' =>  toastTitle('exitosa'),
          'descr_toast' =>  'El pedido fue entregado.'
        ]);
      } else {

        // cerrar modal
        $this->closeEntregarOrderModal();

        $this->dispatch('toast-event', toast_data: [
          'event_type'  =>  'info',
          'title_toast' =>  toastTitle('', true),
          'descr_toast' =>  $result['message']
        ]);
      }
    } catch (\Exception $e) {

      // cerrar modal
      $this->closeEntregarOrderModal();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  $result['message']
      ]);
    }
  }

  /**
   * Buscar mis pedidos
   * @return mixed
   */
  public function searchOrders()
  {
    return Order::with([
      'products' => function ($query) {
        $query->withTrashed();
      },
      'status',
      'sale',
      'user' => function ($query) {
        $query->withTrashed();
      }
    ])
      ->when($this->search_order, function ($query) {
        $query->where('order_code', 'like', '%' . $this->search_order . '%')
          ->orWhereHas('user', function ($q) {
            $q->withTrashed()
              ->where('name', 'like', '%' . $this->search_order . '%')
              ->orWhere('email', 'like', '%' . $this->search_order . '%');
          });
      })
      ->when($this->search_payment_status, function ($query) {
        $query->where('payment_status', $this->search_payment_status);
      })
      ->when($this->search_order_status, function ($query) {
        $query->where('order_status_id', $this->search_order_status);
      })
      ->when(
        $this->search_start_at && $this->search_end_at,
        function ($query) {
          $query->whereBetween('ordered_at', [
            $this->search_start_at . ' 00:00:00',
            $this->search_end_at . ' 23:59:59'
          ]);
        }
      )
      ->when(
        $this->search_start_at && !$this->search_end_at,
        function ($query) {
          $query->where('ordered_at', '>=', $this->search_start_at . ' 00:00:00');
        }
      )
      ->when(
        !$this->search_start_at && $this->search_end_at,
        function ($query) {
          $query->where('ordered_at', '<=', $this->search_end_at . ' 23:59:59');
        }
      )
      ->orderBy('created_at', 'desc')
      ->paginate(10);
  }

  /**
   * resetear la paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar campos de busqueda
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset(['search_order', 'search_payment_status', 'search_order_status', 'search_start_at', 'search_end_at']);
  }


  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $orders = $this->searchOrders();
    return view('livewire.sales.list-orders', compact('orders'));
  }
}
