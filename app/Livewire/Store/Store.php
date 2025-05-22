<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use App\Services\Sale\OrderService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class Store extends Component
{

  use WithPagination;

  // busquedas
  #[Url]
  public $search_products = '';

  #[Url]
  public $search_by_tag = '';

  // etiquetas para busqueda en tienda
  public Collection $tags;

  // preferencia de pago MP
  public ?string $preference_id = null;

  // existe usuario en sesion
  public $is_logged_in = false;

  // usuario
  public User|null $user = null;

  // carrito
  public Collection $products_for_cart;
  public bool $show_cart_modal = false;
  public float $total_for_sale = 0;
  public int $cart_total_items = 0;

  // modal de confirmacion
  public bool $show_confirmation_modal = false;

  /**
   * boot de constantes
   * @return void
   */
  public function boot(): void
  {
    $this->tags = Tag::all();
  }

  /**
   * montar datos
   * @return void
   */
  public function mount(): void
  {
    // existe usuario en sesion
    $this->is_logged_in = Auth::check();

    // capturar usuario
    ($this->is_logged_in) ? $this->user = Auth::user() : null;

    // iniciar carrito de compras vacio
    $this->setProductsForCart();

    // Verificar si existe un carrito en la sesion
    if (Session::has('products_for_cart')) {
      $this->products_for_cart = collect(Session::get('products_for_cart'));
    }
  }

  /**
   * preparar coleccion de productos a vender
   * esta coleccion tendra los productos buscados y elegidos en el modal
   * de registro de ventas
   * ['products_for_cart' => []]
   * @return void
   */
  protected function setProductsForCart(): void
  {
    $this->fill([
      'products_for_cart' => collect()
    ]);
  }

  /**
   * Muestra el modal del carrito de compras
   * estableciendo la propiedad show_cart_modal en true
   * @return void
   */
  public function showCartModal(): void
  {
    // debe estar loggeado para poder usar el carrito de compras
    if (!$this->is_logged_in) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'Para poder usar el carrito y hacer pedidos debe registrarse e iniciar sesión.'
      ]);

      return;
    }

    // calcular total, si hay productos
    if (!$this->products_for_cart->isEmpty()) {
      $this->calculateTotalForSale();
    }

    $this->show_cart_modal = true;
  }

  /**
   * Cierra el modal del carrito de compras
   * @return void
   */
  public function hideCartModal(): void
  {
    $this->show_cart_modal = false;
    $this->resetValidation(); // Limpia todos los errores
  }

  /**
   * agregar un item producto al carrito
   * @param Product $product
   * @return void
   */
  public function addToCart(Product $product): void
  {
    // debe estar loggeado para poder usar el carrito de compras
    if (!$this->is_logged_in) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'Para poder usar el carrito y hacer pedidos debe registrarse e iniciar sesión.'
      ]);

      return;
    }

    // Si el producto ya existe, retornar
    if ($this->products_for_cart->contains(
      function ($product_on_cart) use ($product) {
        return $product_on_cart['product']->id === $product->id;
      }
    )) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'Ya tiene este producto en su carrito de compras!'
      ]);

      return;
    }

    // obtener el precio por defecto
    $default_price = $product->defaultPrice();

    // el detalle es una combinacion de datos de precio, cantidad y descripcion
    // depende del precio o precios del producto (por defecto o seleccionado)
    $details = $default_price->description . ' (' . $default_price->quantity . ') a $' . $default_price->price;

    // agregar producto a la coleccion
    $this->products_for_cart->push([
      'product'           => $product,
      'selected_price_id' => $default_price->id,
      'price'             => $default_price->price,
      'details'           => $details,
      'order_quantity'    => 1,
      'unit_price'        => $default_price->price,
      'subtotal_price'    => $default_price->price,
    ]);

    // calcular total
    $this->calculateTotalForSale();
    $this->showCartModal();
  }

  /**
   * quitar producto de la lista de productos a vender
   * @param $key posicion de la lista
   * @return void
   */
  public function removeProductForSale(int $key): void
  {
    $this->products_for_cart->pull($key);
    $this->calculateTotalForSale();
    $this->resetValidation(); // Limpia todos los errores
  }

  /**
   * calcular el costo total de la venta
   * reduciendo los subtotales
   * @return void
   */
  public function calculateTotalForSale(): void
  {
    $this->total_for_sale = $this->products_for_cart->reduce(function ($carry, $product) {
      return $carry + $product['subtotal_price'];
    }, 0);
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
    $products = $this->products_for_cart->toArray();

    // obtener el producto y el precio seleccionado
    $productForSale = $products[$productIndex];
    $product        = $productForSale['product'];
    $selected_price = $product->prices->find($priceId);

    if (!$selected_price) {
      return;
    }

    // preparar un nuevo detalle segun el nuevo precio elegido
    $details = $selected_price->description . ' (' . $selected_price->quantity . ') a $' . $selected_price->price;

    // actualizar los valores en el array
    $products[$productIndex]['selected_price_id'] = $selected_price->id;
    $products[$productIndex]['price']             = $selected_price->price;
    $products[$productIndex]['details']           = $details;
    $products[$productIndex]['unit_price']        = $selected_price->price;
    $products[$productIndex]['order_quantity']    = 1; // reiniciar cantidad a 1
    $products[$productIndex]['subtotal_price']    = $selected_price->price;

    // reasignar la coleccion completa
    $this->products_for_cart = collect($products);

    // recalcular total
    $this->calculateTotalForSale();
  }

  /**
   * actualizar subtotal cuando cambia la cantidad del producto a vender
   * @param mixed $value nuevo valor de la propiedad
   * @param string $key nombre de la propiedad actualizada
   * @return void
   */
  public function updatedProductsForCart($value, $key): void
  {
    // verificar si el cambio es en order_quantity
    if (str_contains($key, 'order_quantity')) {
      if (!is_string($value) || !ctype_digit($value) || (int)$value <= 0) {
        return;
      }

      // obtener el indice del producto
      $index = explode('.', $key)[0];

      // obtener la coleccion actual
      $products = collect($this->products_for_cart);

      // obtener el item a modificar
      $item = $products[$index];

      // actualizar el subtotal con el precio unitario actual
      $item['subtotal_price'] = (int)$value * $item['unit_price'];

      // actualizar el item en la coleccion
      $products[$index] = $item;

      // reasignar la coleccion
      $this->products_for_cart = $products;

      // calcular total
      $this->calculateTotalForSale();
    }
  }

  /**
   * mostrar un modal de confirmacion para realizar el pedido
   * @return void
   */
  public function showConfirmationModal(): void
  {
    $this->show_confirmation_modal = true;
  }

  /**
   * cerrar el modal de confirmacion para realizar el pedido
   * @return void
   */
  public function closeConfirmationModal(): void
  {
    $this->show_confirmation_modal = false;
  }

  /**
   * * hacer el pedido creando una orden
   * * proceder al pago
   * almacena temporalmente el carrito en sesion
   */
  public function proceedToCheckout()
  {
    try {

      // validar productos de carrito
      $this->validate([
        'products_for_cart'                     => ['required', 'array', 'min:1'],
        'products_for_cart.*.product'           => ['required'],
        'products_for_cart.*.selected_price_id' => ['required'],
        'products_for_cart.*.order_quantity'    => ['required', 'integer', 'min:1'],
      ], [
        'products_for_cart.required'                  => 'Debe agregar al menos un producto al carrito',
        'products_for_cart.min'                       => 'Debe agregar al menos un producto al carrito',
        'products_for_cart.*.order_quantity.required' => 'Tiene que elegir una cantidad a comprar',
        'products_for_cart.*.order_quantity.min'      => 'La cantidad a comprar tiene que ser mayor a 0',
        'products_for_cart.*.order_quantity.integer'  => 'La cantidad a comprar tiene que ser un numero',
      ]);

      // validar stock disponible para cada producto
      foreach ($this->products_for_cart as $index => $product_for_cart) {

        $product = $product_for_cart['product'];
        $selected_price = $product->prices->find($product_for_cart['selected_price_id']);

        // Calcular cantidad total que se intenta vender
        $total_quantity_to_sell = $selected_price->quantity * $product_for_cart['order_quantity'];

        // Obtener stock disponible
        $available_stock = $product->getTotalStockAttribute();

        if ($total_quantity_to_sell > $available_stock) {
          $this->addError(
            "products_for_cart.{$index}.order_quantity",
            "No tenemos stock suficiente para {$product->product_name}. " .
              "Stock disponible: {$available_stock} unidades. " .
              "Cantidad solicitada: {$total_quantity_to_sell} unidades."
          );
          return;
        }
      }

      // si no ha verificado el email, no puede continuar
      if ($this->user->email_verified_at === null) {
        // Guardar el carrito con una duración específica (e.g., 24 horas)
        Session::put('products_for_cart', $this->products_for_cart);
        // Redireccionar a la verificación de email
        return redirect()->route('verification.notice');
      }

      // Si ya está verificado, proceder normalmente
      $order_service = new OrderService();
      $order = $order_service->createOrder($this->products_for_cart, $this->user, $this->total_for_sale);

      session()->flash('operation-success', toastSuccessBody('pedido', 'realizado'));
      return $this->redirectRoute('store-store-cart-index', ['id' => $order->id]);

    } catch (\Illuminate\Validation\ValidationException $e) {

      $this->show_cart_modal = true; // mantener modal abierto
      throw $e; // re lanzar la excepcion para que livewire maneje los errores

    } catch (\Illuminate\Database\QueryException $e) {

      Log::error('Error de base de datos al crear orden:', [
        'message' => $e->getMessage(),
        'code' => $e->getCode()
      ]);

      session()->flash('operation-error', 'Error al procesar el pedido. Por favor intente nuevamente.');
      return;

    } catch (\Exception $e) {

      Log::error('Error general al crear orden:', [
        'message' => $e->getMessage(),
        'code' => $e->getCode()
      ]);

      session()->flash('operation-error', 'Ocurrió un error inesperado. Por favor intente nuevamente.');
      return;
    }
  }

  /**
   * vaciar el carrito
   * @return void
   */
  public function resetCart(): void
  {
    $this->setProductsForCart();
    Session::forget('products_for_cart');
    $this->resetValidation(); // Limpia todos los errores
  }

  /**
   * reiniciar paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar filtros de busqueda
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset(['search_products', 'search_by_tag']);
    $this->resetPagination();
  }

  /**
   * buscar productos
   * @return mixed
   */
  public function searchProducts()
  {
    $products = Product::with('tags')
      ->where('product_in_store', true)
      ->when($this->search_products, function ($query) {
        $query->where('product_name', 'like', '%' . $this->search_products . '%');
      })
      ->when($this->search_by_tag, function ($query) {
        $query->whereHas('tags', function ($q) {
          $q->where('tags.id', (int) $this->search_by_tag);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $products;
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $products = $this->searchProducts();
    return view('livewire.store.store', compact('products'));
  }
}
