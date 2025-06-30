<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use Livewire\Component;
use Illuminate\View\View;
use App\Models\DatoNegocio;
use App\Models\OrderStatus;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Services\Pdf\PdfService;
use App\Services\Sale\SaleService;
use App\Services\User\UserService;
use Illuminate\Support\Collection;

class ListSales extends Component
{
  use WithPagination;

  // * ventas en la lista

  #[Url]
  public $search_sale = '';

  #[Url]
  public $search_start_at = '';

  #[Url]
  public $search_end_at = '';


  // * productos en el modal

  #[Url]
  public $search_product = '';

  // modal de registro de ventas
  public bool $show_new_sale_modal = false;

  // productos a vender (modal)
  public Collection $products_for_sale;
  public $total_for_sale = 0;

  // busqueda de clientes
  public $user_search = '';
  public $selected_user_id = null;
  public $users = [];

  // * modal ver venta
  public bool $show_sale_modal = false;
  public $selected_sale = null;
  public $details_user = null;
  public $details_store = null;

  // * modal comprobante de venta (o pago)
  public bool $show_payment_modal = false;
  public $selected_sale_payment = null;

  // posibles estados de la entrega del producto
  public $order_status_pendiente;
  public $order_status_entregado;
  public $order_status_cancelado;

  // tipos de ventas
  public $sale_type_web = '';
  public $sale_type_presencial = ''; // provisorio

  /**
   * montar datos
   * @return void
   */
  public function mount(): void
  {
    $this->setProductsForSale();

    $this->order_status_pendiente = OrderStatus::ORDER_STATUS_PENDIENTE();
    $this->order_status_entregado = OrderStatus::ORDER_STATUS_ENTREGADO();
    $this->order_status_cancelado = OrderStatus::ORDER_STATUS_CANCELADO();

    $this->sale_type_web = Sale::SALE_TYPE_WEB();
    $this->sale_type_presencial = Sale::SALE_TYPE_PRESENCIAL();

    if ($pending_sale_id = session('pending_sale_id')) {
      $sale = Sale::find($pending_sale_id);
      if ($sale) {
        $this->openShowSaleModal($sale);
      }
    }
  }

  /**
   * al escribir en el input de busqueda de usuarios
   * filtra la busqueda por nombre o email coincidentes
   */
  public function updatedUserSearch($value)
  {
    if (strlen($value) >= 2) {
      $this->users = User::role('cliente')
        ->where(function ($query) use ($value) {
          $query->where('name', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%");
        })
        ->limit(10)
        ->get();
    } else {
      $this->users = [];
    }
  }

  /**
   * busca el usuario elegido y obtiene
   * nombre y email
   */
  public function selectUser($id)
  {
    $this->selected_user_id = $id;
    $user = User::find($id);
    $this->user_search = $user->name . ' - ' . $user->email;
  }

  /**
   * preparar coleccion de productos a vender
   * esta coleccion tendra los productos buscados y elegidos en el modal
   * de registro de ventas
   * ['products_for_sale' => []]
   * @return void
   */
  protected function setProductsForSale(): void
  {
    $this->fill([
      'products_for_sale' => collect()
    ]);
  }

  /**
   * agregar producto a la lista de productos a vender
   * @param Product $product
   * @return void
   */
  public function addProductForSale(Product $product): void
  {
    // Si el producto ya existe, retornar
    if ($this->products_for_sale->contains(
      function ($product_for_sale) use ($product) {
        return $product_for_sale['product']->id === $product->id;
      }
    )) {

      $this->dispatch('already-on-list');
      return;
    }

    // Obtener el precio por defecto
    $default_price = $product->defaultPrice();

    // agregar producto a la coleccion
    $this->products_for_sale->push([
      'product'           => $product,
      'selected_price_id' => $default_price->id, // Agregar ID del precio seleccionado
      'price'             => $default_price->price,
      'sale_quantity'     => 1,
      'unit_price'        => $default_price->price,
      'subtotal_price'    => $default_price->price,
    ]);

    // calcular total
    $this->calculateTotalForSale();
  }

  /**
   * quitar producto de la lista de productos a vender
   * @param $key posicion de la lista
   * @return void
   */
  public function removeProductForSale(int $key): void
  {
    $this->products_for_sale->pull($key);
    $this->calculateTotalForSale();
  }

  /**
   * al elegir un precio para el producto en la lista de venta, actualizar el producto
   * se define el precio elegido, la cantidad pasa a 1, se indica el precio unitario
   * y se recalcula el precio subtotal del producto
   * @return void
   */
  public function updateSelectedPrice($productIndex, $priceId): void
  {
    // convertir la coleccion en array para modificarla
    $products = $this->products_for_sale->toArray();

    // obtener el producto y el precio seleccionado
    $productForSale = $products[$productIndex];
    $product = $productForSale['product'];
    $selectedPrice = $product->prices->find($priceId);

    if (!$selectedPrice) {
      return;
    }

    // actualizar los valores en el array
    $products[$productIndex]['selected_price_id'] = $selectedPrice->id;
    $products[$productIndex]['price'] = $selectedPrice->price;
    $products[$productIndex]['unit_price'] = $selectedPrice->price;
    $products[$productIndex]['sale_quantity'] = 1; // reiniciar cantidad a 1
    $products[$productIndex]['subtotal_price'] = $selectedPrice->price; // el subtotal sera igual al precio unitario * 1

    // reasignar la coleccion completa
    $this->products_for_sale = collect($products);

    // recalcular total
    $this->calculateTotalForSale();
  }

  /**
   * actualizar subtotal cuando cambia la cantidad del producto a vender
   * @param mixed $value nuevo valor de la propiedad
   * @param string $key nombre de la propiedad actualizada
   * @return void
   */
  public function updatedProductsForSale($value, $key): void
  {
    // verificar si el cambio es en sale_quantity
    if (str_contains($key, 'sale_quantity')) {
      if (!is_string($value) || !ctype_digit($value) || (int)$value <= 0) {
        return;
      }

      // obtener el indice del producto
      $index = explode('.', $key)[0];

      // obtener la coleccion actual
      $products = collect($this->products_for_sale);

      // obtener el item a modificar
      $item = $products[$index];

      // actualizar el subtotal con el precio unitario actual
      $item['subtotal_price'] = (int)$value * $item['unit_price'];

      // actualizar el item en la coleccion
      $products[$index] = $item;

      // reasignar la coleccion
      $this->products_for_sale = $products;

      // calcular total
      $this->calculateTotalForSale();
    }
  }

  /**
   * calcular el costo total de la venta
   * reduciendo los subtotales
   * @return void
   */
  public function calculateTotalForSale(): void
  {
    $this->total_for_sale = $this->products_for_sale->reduce(function ($carry, $product) {
      return $carry + $product['subtotal_price'];
    }, 0);
  }

  /**
   * abrir modal de registro de venta
   * @return void
   */
  public function openNewSaleModal(): void
  {
    $this->show_new_sale_modal = true;
  }

  /**
   * cerrar modal de registro de venta
   * @return void
   */
  public function closeNewSaleModal(): void
  {
    $this->setProductsForSale();
    $this->show_new_sale_modal = false;
  }

  /**
   * abrir modal ver venta
   * @return void
   */
  public function openShowSaleModal(Sale $sale): void
  {
    $selected_sale = Sale::with([
      'products' => function ($query) {
        $query->withTrashed();
      },
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
    ])->findOrFail($sale->id);

    $this->selected_sale = $selected_sale;

    if ($selected_sale->user) {

      $user_service = new UserService();
      $this->details_user = $user_service->getUserData($selected_sale->user->id);
    } else {

      $this->details_user = [
        'usuario'   => 'cliente no registrado',
        'perfil'    => '-',
        'direccion' => '-'
      ];
    }

    $this->details_store = DatoNegocio::obtenerTodos();

    $this->show_sale_modal = true;
  }

  /**
   * cerrar modal ver venta
   * @return void
   */
  public function closeShowSaleModal(): void
  {
    $this->show_sale_modal = false;
    $this->selected_sale = null;
    $this->details_user = null;
  }

  /**
   * Buscar ventas
   * por id, cliente, fechas
   */
  public function searchSales()
  {
    return Sale::with(['user' => function ($query) {
      $query->withTrashed();
    }, 'order'])
      ->when(
        $this->search_sale,
        function ($query) {
          $query->where('id', '=', $this->search_sale)
            ->orWhereHas('user', function ($query) {
              $query->withTrashed()
                ->role('cliente')
                ->where('name', 'like', '%' . $this->search_sale . '%')
                ->orWhere('email', 'like', '%' . $this->search_sale . '%');
            });
        }
      )
      ->when(
        $this->search_start_at && $this->search_end_at,
        function ($query) {
          $query->whereBetween('sold_on', [
            $this->search_start_at . ' 00:00:00',
            $this->search_end_at . ' 23:59:59'
          ]);
        }
      )
      ->when(
        $this->search_start_at && !$this->search_end_at,
        function ($query) {
          $query->where('sold_on', '>=', $this->search_start_at . ' 00:00:00');
        }
      )
      ->when(
        !$this->search_start_at && $this->search_end_at,
        function ($query) {
          $query->where('sold_on', '<=', $this->search_end_at . ' 23:59:59');
        }
      )
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  /**
   * buscar productos en el modal de venta
   * retorna productos disponibles para la venta con su stock y precios
   */
  public function searchProducts()
  {
    return Product::with(['stocks', 'prices'])
      ->whereHas('stocks', function ($query) {
        $query->where('quantity_left', '>', 0)
          ->where('expired_at', '>', now()->format('Y-m-d H:i:s'));
      })
      ->when($this->search_product, function ($query) {
        $query->where('product_name', 'like', '%' . $this->search_product . '%');
      })
      ->paginate(5);
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
    $this->reset(['search_product', 'search_sale', 'search_start_at', 'search_end_at']);
  }

  /**
   * guardar venta
   * registra una venta con productos y decrementa stock
   * valida si hay stock suficiente
   */
  public function save()
  {
    // validar que haya productos en la lista
    $validated = $this->validate([
      'selected_user_id'                      => ['nullable'],
      'products_for_sale'                     => ['required', 'array', 'min:1'],
      'products_for_sale.*.product'           => ['required'],
      'products_for_sale.*.selected_price_id' => ['required'],
      'products_for_sale.*.sale_quantity'     => ['required', 'integer', 'min:1'],
      'products_for_sale.*.unit_price'        => ['required'],
      'products_for_sale.*.subtotal_price'    => ['required'],
    ], [
      'products_for_sale.required' => 'Debe agregar al menos un producto a la venta',
      'products_for_sale.min'      => 'Debe agregar al menos un producto a la venta',
      'products_for_sale.*.sale_quantity.required' => 'La cantidad a vender es requerida',
      'products_for_sale.*.sale_quantity.min'      => 'La cantidad a vender debe ser mayor a 0',
      'products_for_sale.*.sale_quantity.integer'  => 'La cantidad a vender debe ser un numero',
    ]);

    // Validar stock disponible para cada producto
    foreach ($this->products_for_sale as $index => $product_for_sale) {

      $product = $product_for_sale['product'];
      $selectedPrice = $product->prices->find($product_for_sale['selected_price_id']);

      // Calcular cantidad total que se intenta vender
      $totalQuantityToSell = $selectedPrice->quantity * $product_for_sale['sale_quantity'];

      // Obtener stock disponible
      // siempre contando no vencidos.
      $availableStock = $product->getTotalStockAttribute();

      if ($totalQuantityToSell > $availableStock) {
        $this->addError(
          "products_for_sale.{$index}.sale_quantity",
          "Stock insuficiente para {$product->product_name}. " .
            "Stock disponible: {$availableStock} unidades. " .
            "Cantidad solicitada: {$totalQuantityToSell} unidades."
        );
        return;
      }
    }

    try {

      // preparar array de datos para la venta
      $new_sale_data = [
        'user_id'      => $this->selected_user_id,
        'client_type'  => $this->selected_user_id ? Sale::CLIENT_TYPE_REGISTERED() : Sale::CLIENT_TYPE_UNREGISTERED(),
        'sale_type'    => Sale::SALE_TYPE_PRESENCIAL(),
        'sold_on'      => now(),
        'payment_type' => 'efectivo',
        'total_price'  => $this->total_for_sale,
        'products'     => $validated['products_for_sale']
      ];

      //dd($new_sale_data);

      // crear venta presencial
      $sale_service = new SaleService();
      $sale = $sale_service->createPresentialSale($new_sale_data);

      // crear comprobante de venta
      $pdf_service = new PdfService();
      $pdf_service->generateSalePDF($sale, $sale_service->generateSaleData($sale));

      $this->closeNewSaleModal();

      $this->reset(['selected_user_id', 'user_search', 'users', 'search_product', 'total_for_sale']);

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'success',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  'Venta realizada.'
      ]);
    } catch (\Exception $e) {

      $this->closeNewSaleModal();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  $e->getMessage()
      ]);
    }
  }

  /**
   * mostrar modal de comprobante de pago
   * @param int $id
   * @return void
   */
  public function showPayment(int $id): void
  {
    // venta con productos y orden
    $sale = Sale::with(['order', 'products'])->findOrFail($id);

    // si la venta no tiene orden o pedido previo, mostrar comprobante pdf
    if (!$sale->order()->exists()) {
      $this->openPdfSale($sale->id);
      return;
    }

    // proceder a mostrar comprobante del pedido
    $this->selected_sale_payment = $sale;
    $this->show_payment_modal = true;
  }

  /**
   * cerrar modal de comprobante de pago
   * @return void
   */
  public function closePayment(): void
  {
    $this->show_payment_modal = false;
    $this->selected_sale_payment = null;
  }

  /**
   * abrir pdf en una nueva pestaña,
   * para poder visualizar y descargar.
   * @param int $id id de venta base para el pdf
   * @return void
   */
  public function openPdfSale($id): void
  {
    // denerar URL para ver el pdf
    $pdfUrl = route('open-pdf-sale', ['id' => $id]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $sales = $this->searchSales();
    $available_products = $this->searchProducts();

    return view('livewire.sales.list-sales', compact('sales', 'available_products'));
  }
}
