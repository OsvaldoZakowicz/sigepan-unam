<?php

namespace App\Livewire\Stocks;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class ListProducts extends Component
{
  use WithPagination;

  #[Url]
  public string $search_product = '';

  #[Url]
  public string $tag_filter = '';

  public Collection $tags;

  /**
   * boot de datos constantes
   * @return void
  */
  public function boot(): void
  {
    $this->tags = Tag::all();
  }

  /**
   * SOFT DLETE: borrar un producto
   * solo cuando no este asociado a pedidos
   * @param Product $product
   * @return void
   */
  public function delete(Product $product): void
  {
    // todo: si hay pedidos pendientes, no borrar

    // retirar de la tienda
    $product->product_in_store = false;
    $product->save();

    $product->delete();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'success',
      'title_toast' =>  toastTitle('exitosa'),
      'descr_toast' =>  'El producto fue eliminado y retirado de la tienda.'
    ]);
  }

  /**
   * restaurar un producto borrado
   * @param int $id del producto
   * @return void
  */
  public function restore(int $id): void
  {
    Product::withTrashed()->where('id', $id)->restore();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'success',
      'title_toast' =>  toastTitle('exitosa'),
      'descr_toast' =>  'El producto fue restaurado.'
    ]);
  }

  /**
   * editar un producto
   * solo cuando no tenga pedidos pendientes
   * @param Product $product
   * @return void
   */
  public function edit(Product $product): void
  {
    // todo: si hay pedidos pendientes, no editar

    $this->redirectRoute('stocks-products-edit', $product->id, true, true);
  }

  /**
   * buscar productos
   * @return mixed
  */
  public function searchProducts()
  {
    $products = Product::withTrashed()
      ->with('tags')
      ->when($this->search_product, function ($query) {
        $query->where('product_name', 'like', '%' . $this->search_product . '%')
              ->orWhere('product_price', '<=', (float) $this->search_product);
      })
      ->when($this->tag_filter, function ($query) {
        $query->whereHas('tags', function ($q) {
          $q->where('tags.id', (int) $this->tag_filter);
        });
      })
      ->orderBy('deleted_at', 'asc') // primero los NO borrados (deleted_at null)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $products;
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
    $this->reset(['search_product', 'tag_filter', 'in_store_filter']);
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $products = $this->searchProducts();
    return view('livewire.stocks.list-products', compact('products'));
  }
}
