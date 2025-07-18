<?php

namespace App\Livewire\Store;

use App\Models\Sale;
use App\Models\DatoNegocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class Purchases extends Component
{
  use WithPagination;

  #[Url]
  public $search_purchase = ''; // id, tipo de venta o codigo de orden

  #[Url]
  public $search_purchase_date = ''; // fecha de compra

  // modal de comprobante de pago
  public bool $show_payment_modal = false;
  public $selected_sale = null;

  // tipos de ventas (y por ende, compras)
  public $sale_type_web = '';
  public $sale_type_presencial = '';

  // establecimiento, para comprobante
  public $establecimiento = '';

  /**
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    $this->sale_type_web = Sale::SALE_TYPE_WEB();
    $this->sale_type_presencial = Sale::SALE_TYPE_PRESENCIAL();

    // preparar dato de establecimiento
    $razon_social = DatoNegocio::obtenerValor('razon_social') ?? '';
    $cuit = DatoNegocio::obtenerValor('cuit') ?? '';
    $inicio_actividades = DatoNegocio::obtenerValor('inicio_actividades') ?
      date('d-m-Y', strtotime(DatoNegocio::obtenerValor('inicio_actividades'))) : '';

    $this->establecimiento = $razon_social . ' CUIT: ' . $cuit . ' inicio de actividades: ' . $inicio_actividades;
  }

  /**
   * mostrar modal de comprobante de pago
   * @param int $id
   * @return void
   */
  public function showPayment(int $id): void
  {
    // venta con productos y orden
    $this->selected_sale = Sale::with(['order', 'products'])->findOrFail($id);
    $this->show_payment_modal = true;
  }

  /**
   * cerrar modal de comprobante de pago
   * @return void
   */
  public function closePayment(): void
  {
    $this->show_payment_modal = false;
    $this->selected_sale = null;
  }

  /**
   * comprobante de compra PDF compra presencial.
   * abrir pdf en una nueva pestaña,
   * para poder visualizar y descargar.
   * @param int $id id de venta base para el pdf
   * @return void
   */
  public function openPdfSale($id): void
  {
    // generar URL para ver el pdf
    $pdfUrl = route('open-pdf-purchase', ['id' => $id]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
  }

  /**
   * buscar mis compras
   * @return mixed
   */
  public function searchPurchases()
  {
    return Sale::with([
      'products' => function ($query) {
        $query->withTrashed();
      },
      'order'
    ])
      ->where('user_id', Auth::id())
      ->when($this->search_purchase, function ($query) {
        $query->where(function ($q) {
          $q->where('id', 'like', '%' . $this->search_purchase . '%')
            ->orWhere('payment_type', 'like', '%' . $this->search_purchase . '%')
            ->orWhere('sale_type', 'like', '%' . $this->search_purchase . '%')
            ->orWhereHas('order', function ($orderQuery) {
              $orderQuery->where('order_code', 'like', '%' . $this->search_purchase . '%');
            });
        });
      })
      ->when($this->search_purchase_date, function ($query) {
        $query->whereDate('sold_on', $this->search_purchase_date);
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $sales = $this->searchPurchases();
    return view('livewire.store.purchases', compact('sales'));
  }
}
