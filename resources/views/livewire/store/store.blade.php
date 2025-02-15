<div>
    {{-- navegacion de busqueda e icono de carrito --}}
    <header class="w-full px-1 py-4 flex justify-between items-center">

      {{-- busqueda --}}
      <input type="text" name="search-products" placeholder="buscar productos" class="w-1/4 rounded-md text-lg" />

      {{-- ver carrito --}}
      <button
        wire:click="showCartModal()"
        class="mt-auto bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
        >ver carrito
        @if ($cart_total_items > 0)
          <span class="text-neutral-100 bg-red-600 p-1 m-1 rounded-full">{{ $cart_total_items }}</span>
        @endif
      </button>

    </header>

    <main role="store">
      <section role="products-list" class="w-full p-2 flex justify-start items-start gap-6 flex-wrap">
        @forelse ($products as $product)
          <article
            wire:key="{{ $product->id }}"
            class="bg-white rounded-lg shadow-md w-64 h-96 p-4 flex flex-col">
            <img
              src="{{ Storage::url($product->product_image_path) }}"
              alt="{{ $product->product_name }}"
              class="w-full h-48 object-cover rounded-t-lg">
              <div class="flex flex-col gap-2 mt-4">
                <h2 class="text-xl font-bold text-gray-800">{{ $product->product_name }}</h2>
                <h3 class="text-sm text-gray-600">{{ $product->product_short_description }}</h3>
                <h3 class="text-2xl font-bold text-blue-600">${{ number_format($product->product_price, 2) }}</h3>
                <button
                  wire:click="addToCart({{ $product }})"
                  class="mt-auto bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
                  >agregar al carrito
                </button>
              </div>
          </article>
        @empty
          <span>¡Sin productos a la venta!</span>
        @endforelse
      </section>

      <!-- Modal del Carrito -->
      <div
        x-data="{ show: @entangle('show_cart_modal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">

          <div class="fixed inset-0 bg-black opacity-30"></div>

          <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-2xl font-bold">Carrito de Compras</h2>
              <button
                wire:click="$set('show_cart_modal', false)"
                class="mt-auto bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
                >continuar comprando
              </button>
            </div>

            <div class="space-y-4">
              @forelse($cart as $key => $item)
                {{-- item del carrito --}}
                <div wire:key="{{ $item['id'] }}" class="flex items-center justify-between border-b pb-4">
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
                    <input
                      type="number"
                      wire:model.live="cart.{{ $loop->index }}.quantity"
                      wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                      class="w-20 p-1 border rounded" min="1" value="{{ $item['quantity'] }}"
                    />

                    <p class="font-semibold">${{ number_format($item['subtotal'], 2) }}</p>

                    <button
                      wire:click="removeFromCart({{ $key }})"
                      class="text-red-500 hover:text-red-700">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              @empty
                <p class="text-center text-gray-500">El carrito está vacío</p>
              @endforelse

              @if ($cart->isNotEmpty())

                {{-- calculo del total del carrito --}}
                <div class="flex justify-between items-center pt-4">
                  <h3 class="text-xl font-bold">Total:</h3>
                  <p class="text-2xl font-bold text-blue-600">${{ number_format($cart_total, 2) }}</p>
                </div>

                {{-- boton para pedir y pagar con mercado pago --}}
                <button
                  class="mt-auto bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
                  >hacer pedido
                </button>

              @endif
            </div>
          </div>
        </div>
      </div>
    </main>
</div>
