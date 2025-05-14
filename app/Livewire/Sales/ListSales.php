<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Product;
use App\Services\Sale\SaleService;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListSales extends Component
{
  use WithPagination;

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

  /**
   * montar datos
   * @return void
   */
  public function mount(): void
  {
    $this->setProductsForSale();
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
      // todo: avisar que el producto ya esta en la lista
      return;
    }

    // agregar producto a la coleccion
    $this->products_for_sale->push([
      'product'        => $product,
      'sale_quantity'  => 1,
      'unit_price'     => $product->product_price,
      'subtotal_price' => $product->product_price,
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
    // calcular total
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

      // early return si no es un string numerico
      if (!is_string($value) || !ctype_digit($value)) {
        return;
      }

      // early return si al convertirlo no es mayor que 0
      if ((int)$value <= 0) {
        return;
      }

      // obtener el indice del producto
      $index = explode('.', $key)[0];

      // obtener la coleccion actual
      $products = collect($this->products_for_sale);

      // obtener el item a modificar
      $item = $products[$index];

      // actualizar el subtotal
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
   * actualizar total de la venta mientras cambia la cantidad de productos
   * de la lista y cantidades
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
    $this->setProductsForSale(); // reestablecer coleccion de productos!
    $this->show_new_sale_modal = false;
  }

  /**
   * buscar ventas
   */
  public function searchSales()
  {
    return Sale::paginate(10);
  }

  /**
   * buscar productos en el modal de venta
   * retorna productos disponibles para la venta
   */
  public function searchProducts()
  {
    return Product::with('stocks')
      ->whereHas('stocks', function ($query) {
        $query->where('quantity_left', '>', 0);
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
    $this->reset(['search_product']);
  }

  /**
   * guardar venta
   */
  public function save()
  {
    // validar que haya productos en la lista
    $validated = $this->validate([
      'selected_user_id'                   => ['nullable'],
      'products_for_sale'                  => ['required', 'array', 'min:1'],
      'products_for_sale.*.product'        => ['required'],
      'products_for_sale.*.sale_quantity'  => ['required', 'integer', 'min:1'],
      'products_for_sale.*.unit_price'     => ['required'],
      'products_for_sale.*.subtotal_price' => ['required'],
    ], [
      'products_for_sale.required' => 'Debe agregar al menos un producto a la venta',
      'products_for_sale.min'      => 'Debe agregar al menos un producto a la venta',
      'products_for_sale.*.sale_quantity.required' => 'La cantidad es requerida',
      'products_for_sale.*.sale_quantity.min'      => 'La cantidad debe ser mayor a 0',
      'products_for_sale.*.sale_quantity.integer'  => 'La cantidad debe ser un numero',
    ]);

    try {

      // preparar array de datos para la venta
      $new_sale_data = [
        'user_id'      => $this->selected_user_id,
        'client_type'  => $this->selected_user_id ? Sale::CLIENT_TYPE_REGISTERED() : Sale::CLIENT_TYPE_UNREGISTERED(),
        'sale_type'    => Sale::SALE_TYPE_PRESENCIAL(),
        'payment_type' => 'efectivo',
        'total_price'  => $this->total_for_sale,
        'products'     => $validated['products_for_sale']
      ];

      // crear venta presencial
      $sale_service = new SaleService();
      $sale_service->createPresentialSale($new_sale_data);

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
