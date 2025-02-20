<div class="bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900">

  {{-- navegacion de busqueda e icono de carrito --}}
  <header class="max-w-5xl mx-auto mb-6 p-6 flex justify-between items-center">

    <div class="flex items-center gap-4">
      {{-- busqueda --}}
      <input
        type="text"
        name="search_products"
        wire:model.live.debounce.250ms="search_products"
        wire:click="resetPagination()"
        placeholder="buscar productos"
        class="rounded-md text-lg bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
      />

      {{-- busqueda por etiqueta --}}
      <select
        name="search_by_tag"
        wire:model.live="search_by_tag"
        wire:click="resetPagination()"
        class="rounded-md text-lg bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none [&>option:checked]:bg-orange-200 [&>option:hover]:bg-orange-300"
        >
        <option value="">filtrar por etiqueta ...</option>
        @foreach ($tags as $tag)
          <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
        @endforeach
      </select>

      {{-- limpiar busqueda --}}
      <button
        wire:click="resetSearchInputs()"
        class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
        >limpiar filtros
        <span class="text-orange-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </span>
      </button>
    </div>

    {{-- ver carrito --}}
    <button
      wire:click="showCartModal()"
      class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-700 text-white"
      >ver carrito
      <span class="text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </span>
      @if ($cart->count() > 0)
        <span title="tiene elementos en el carrito" class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-orange-100 text-orange-800 border-2 border-orange-900">{{ $cart->count() }}</span>
      @endif
    </button>

  </header>

  <main role="store" class="max-w-5xl	mx-auto">

    {{-- lista de productos --}}
    <section role="products-list" class="w-full grid grid-cols-3 gap-y-8 auto-rows-auto justify-items-center">
      @forelse ($products as $product)
        {{-- card de producto --}}
        <article wire:key="{{ $product->id }}" class="bg-white rounded-lg shadow-lg w-full max-w-[18rem] h-96 flex flex-col justify-start">
          <img src="{{ Storage::url($product->product_image_path) }}" alt="{{ $product->product_name }}" class="w-full h-1/3 object-cover rounded-t-lg"/>
          <div class="flex flex-col gap-2 p-4">
            <h2 class="text-xl font-bold text-gray-700 capitalize">{{ $product->product_name }}</h2>
            <h3 class="text-sm text-gray-600">{{ $product->product_short_description }}</h3>
          </div>
          <div class="w-full px-4 flex flex-wrap gap-2 justify-start items-start">
            @foreach ($product->tags as $tag)
              <span class="py-1 px-2 rounded-md text-xs uppercase text-orange-800 bg-orange-200">{{ $tag->tag_name }}</span>
            @endforeach
          </div>
          <div class="w-full p-4 flex justify-between mt-auto">
            <span class="text-xl font-light text-neutral-700"> ${{ number_format($product->product_price, 2) }}</span>
            <button
              wire:click="addToCart({{ $product }})"
              class="inline-flex justify-between items-center mt-auto text-xs p-1  rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
              >agregar al carrito
              <span class="text-orange-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </span>
            </button>
          </div>
        </article>
      @empty
        <article class="col-start-1 col-span-full">
          <span>¡Vaya! no encontramos productos para esta busqueda. Prueba usando otros filtros.</span>
        </article>
      @endforelse
    </section>

    {{-- modal del carrito --}}
    <div
      x-data="{ show: @entangle('show_cart_modal') }"
      x-show="show"
      x-cloak
      class="fixed inset-0 z-50 overflow-y-auto"
      x-transition>
      <div class="flex items-center justify-center min-h-screen px-4">

        <div class="fixed inset-0 bg-neutral-950 opacity-40"></div>

        <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl text-neutral-700 font-semibold">Carrito de Compras</h2>
            <button
              wire:click="$set('show_cart_modal', false)"
              class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
              >continuar comprando
              <span class="text-orange-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </span>
            </button>
          </div>

          <div class="space-y-4">
            @forelse($cart as $item)
              {{-- item del carrito --}}
              <div wire:key="{{ $loop->index }}" class="flex flex-col items-center justify-between border-b pb-6">
                <div class="flex flex-col w-full relative">
                  {{-- fila item --}}
                  <div class="w-full flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                      <img
                        src="{{ Storage::url($item['product']->product_image_path) }}"
                        alt="{{ $item['product']->product_name }}"
                        class="w-16 h-16 object-cover rounded"
                      />
                      <div>
                        <h3 class="font-semibold">{{ $item['product']->product_name }}</h3>
                        <p class="text-gray-500">${{ number_format($item['product']->product_price, 2) }} c/u</p>
                      </div>
                    </div>
                    <div class="flex items-center space-x-4">
                      {{-- input cantidad --}}
                      <input
                        type="number"
                        wire:model.live="cart.{{ $loop->index }}.quantity"
                        wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                        min="1"
                        onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                        oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = this.value <= 0 ? 1 : this.value"
                        value="{{ $item['quantity'] }}"
                        title="Por favor ingrese un número entero positivo mayor a cero"
                        class="w-24 rounded-md text-lg bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
                      />

                      {{-- precio --}}
                      <p class="font-semibold">${{ number_format($item['subtotal'], 2) }}</p>

                      {{-- papelera individual --}}
                      <button
                        wire:click="removeFromCart({{ $item['id'] }})"
                        class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>

                    </div>
                  </div>
                  {{-- fila error --}}
                  <div x-data="{ show: false, message: '' }" x-on:quantity-error.window="message = $event.detail; show = true" x-show="show" class="absolute -bottom-5 left-0 text-red-500 text-sm" x-cloak>
                    <span x-text="message"></span>
                  </div>
                </div>
              </div>
            @empty
              <div class="flex items-center justify-start">
                <p class="text-center text-gray-600">¡El carrito está vacío!</p>
              </div>
            @endforelse

            @if ($cart->isNotEmpty())

              {{-- calculo del total del carrito --}}
              <div class="flex justify-between items-center pt-4">
                <h3 class="text-xl font-bold">Total:</h3>
                <p class="text-2xl font-bold text-orange-800">${{ number_format($cart_total, 2) }}</p>
              </div>

              {{-- botones de carrito y pago --}}
              <div class="flex justify-between items-center">
                {{-- Botón vaciar carrito --}}
                <button
                    wire:click="resetCart()"
                    class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                    >vaciar carrito
                    <span class="text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </span>
                </button>

                <a
                  wire:navigate
                  href="#"
                  wire:click="proceedToCheckout()"
                  class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-800 text-orange-100"
                  >proceder al pedido
                </a>

              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </main>

  <div role="paginacion" class="max-w-5xl mx-auto">
    <div class="p-10">
      {{ $products->links() }}
    </div>
  </div>

  <footer role="pie-de-pagina" class="w-full bg-neutral-800 text-white p-4 h-96 mt-20">
    {{-- todo --}}
  </footer>
</div>
