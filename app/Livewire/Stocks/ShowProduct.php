<?php

namespace App\Livewire\Stocks;

use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\View\View;
use Livewire\Component;

class ShowProduct extends Component
{
  public Product $product;
  public bool $show_edit_prices_modal = false;

  // precios del producto
  public $prices_list = [];
  public $quantity;
  public $price;
  public $price_description;
  public $is_default = false;

  /**
   * montar datos
   * @return void
   */
  public function mount(int $id): void
  {
    $this->product = Product::findOrFail($id);
  }

  // editar precios
  public function openEditPricesModal(): void
  {
    // Limpiar la lista antes de cargar
    $this->prices_list = [];

    // Cargar precios existentes
    foreach ($this->product->prices as $price) {
      $this->prices_list[] = [
        'id'          => $price->id, // Importante: guardar el ID para actualizar
        'quantity'    => (int)$price->quantity,
        'price'       => (float)$price->price,
        'description' => $price->description,
        'is_default'  => (bool)$price->is_default,
      ];
    }

    $this->show_edit_prices_modal = true;
  }

  // cerrar modal de edicion de precios
  public function closeEditPricesModal(): void
  {
    $this->prices_list = [];
    $this->reset(['quantity', 'price', 'price_description', 'is_default']);
    $this->show_edit_prices_modal = false;
  }

  /**
   * Agregar un precio a la lista
   */
  public function addPrice(): void
  {
    // Validaciones básicas (copiadas exactamente de CreateProduct)
    $this->validate([
      'quantity' => [
        'required',
        'integer',
        'min:1',
        function ($attribute, $value, $fail) {
          // Verifica si ya existe esta cantidad en la lista temporal
          $quantityExists = collect($this->prices_list)->contains(function ($price) use ($value) {
            return $price['quantity'] === (int)$value;
          });

          if ($quantityExists) {
            $fail('Ya existe un precio para esta cantidad.');
          }
        }
      ],
      'price' => ['required', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'min:1'],
      'price_description' => [
        'required',
        'string',
        'min:3',
        function ($attribute, $value, $fail) {
          // Verifica si ya existe esta descripción en la lista temporal
          $descriptionExists = collect($this->prices_list)->contains(function ($price) use ($value) {
            return strtolower($price['description']) === strtolower($value);
          });

          if ($descriptionExists) {
            $fail('Ya existe un precio con esta descripción.');
          }
        }
      ],
    ]);

    // Control del precio predeterminado
    if ($this->is_default) {
      foreach ($this->prices_list as &$price) {
        if ($price['is_default']) {
          $price['is_default'] = false;
        }
      }
    } else if (empty($this->prices_list)) {
      $this->is_default = true;
    }

    // Agregar el nuevo precio a la lista (sin ID porque es nuevo)
    $this->prices_list[] = [
      'id'          => null, // Nuevo precio, no tiene ID
      'quantity'    => (int)$this->quantity,
      'price'       => (float)$this->price,
      'description' => $this->price_description,
      'is_default'  => $this->is_default,
    ];

    // Ordenar la lista por cantidad
    usort($this->prices_list, function ($a, $b) {
      return $a['quantity'] <=> $b['quantity'];
    });

    // Limpiar el formulario
    $this->reset(['quantity', 'price', 'price_description', 'is_default']);

    // Notificar éxito
    $this->dispatch('toast-event', toast_data: [
      'event_type' => 'success',
      'title_toast' => toastTitle('', true),
      'descr_toast' => 'Precio agregado correctamente'
    ]);
  }

  /**
   * Remover un precio de la lista
   */
  public function removePrice($index): void
  {
    unset($this->prices_list[$index]);
    $this->prices_list = array_values($this->prices_list);
  }

  /**
   * guardar precios para el producto
   */
  public function save()
  {
    $validated = $this->validate([
      'prices_list' => ['required', 'array', 'min:1'],
    ]);

    // Validar que haya exactamente un precio predeterminado
    $defaultPrices = collect($this->prices_list)->filter(function ($price) {
      return $price['is_default'];
    })->count();

    if ($defaultPrices !== 1) {
      $this->addError('prices_list', 'Debe haber exactamente un precio predeterminado');
      return;
    }

    try {

      // Usar el servicio para actualizar precios
      $product_service = new ProductService();
      $product_service->updateProductPrices($this->product, $this->prices_list);

      // Recargar el producto para mostrar los cambios
      $this->product->refresh();

      // Cerrar modal
      $this->closeEditPricesModal();

      // Notificar éxito
      session()->flash('operation-success', 'Precios actualizados correctamente');
    } catch (\Exception $e) {
      session()->flash('operation-error', 'Error: ' . $e->getMessage() . ' Contacte al Administrador.');
    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.stocks.show-product');
  }
}
