<?php

namespace App\Livewire\Store;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Sale;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class Orders extends Component
{
  use WithPagination;

  public bool $show_details_modal = false;
  public bool $show_payment_modal = false;
  public $details_order = null;
  public $payment_order = null;

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
