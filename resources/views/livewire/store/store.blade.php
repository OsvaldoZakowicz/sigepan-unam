<div class="mt-20 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900">

  {{-- navegacion de busqueda e icono de carrito --}}
  <header class="fixed z-10 w-full mb-6 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 shadow-[0_4px_6px_-1px_rgba(51,51,51,0.5)]">
    <div class="max-w-5xl mx-auto p-6 flex justify-between items-center">
      {{-- buscar --}}
      <div class="flex items-center gap-4">
        {{-- busqueda por nombre --}}
        <input
          type="text"
          name="search_products"
          wire:model.live.debounce.250ms="search_products"
          wire:click="resetPagination()"
          placeholder="buscar productos"
          class="rounded-md py-1 px-2 text-lg bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
        />
        {{-- busqueda por etiqueta --}}
        <select
          name="search_by_tag"
          wire:model.live="search_by_tag"
          wire:click="resetPagination()"
          class="rounded-md text-lg py-1 px-2 pr-8 bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none [&>option:checked]:bg-orange-200 [&>option:hover]:bg-orange-300"
          >
          <option value="">filtrar por etiqueta ...</option>
          @foreach ($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
          @endforeach
        </select>
        {{-- limpiar busqueda --}}
        <button
          wire:click="resetSearchInputs()"
          class="inline-flex py-1 px-2 justify-between items-center mt-auto rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
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
        class="inline-flex justify-between items-center mt-auto py-1 px-2 rounded border-2 border-orange-950 bg-orange-700 text-white"
        >ver carrito
        <span class="text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </span>
        @if ($products_for_cart->count() > 0)
          <span title="tiene elementos en el carrito" class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-orange-100 text-orange-800 border-2 border-orange-900">{{ $products_for_cart->count() }}</span>
        @endif
      </button>
    </div>
  </header>

  <main role="store" class="max-w-5xl	mx-auto pt-28">

    {{-- lista de productos --}}
    <section role="products-list" class="w-full grid grid-cols-3 gap-y-8 auto-rows-auto justify-items-center">
      @forelse ($products as $product)
        {{-- card de producto --}}
        <article
          wire:key="{{ $product->id }}"
          class="bg-white rounded-lg shadow-lg w-full max-w-[18rem] h-96 flex flex-col justify-start">
          {{-- imagen del producto --}}
          <img
            src="{{ Storage::url($product->product_image_path) }}"
            alt="{{ $product->product_name }}"
            class="w-full h-1/3 object-cover rounded-t-lg"
          />
          {{-- nombre, descripcion y stock --}}
          <div class="flex flex-col gap-1 px-4 py-2">
            <h2 class="text-lg font-bold text-neutral-800 capitalize">{{ $product->product_name }}</h2>
            <span class="text-sm text-neutral-600">{{ $product->product_short_description }}</span>
            {{-- stock --}}
            <span class="font-semibold text-xs uppercase">
              @if ($product->getTotalStockAttribute() !== 0)
                <span class="text-emerald-600">{{ $product->getTotalStockAttribute() }} unidades disponibles!</span>
              @else
                <span class="text-neutral-500">sin stock!</span>
              @endif
            </span>
          </div>
          {{-- etiquetas de clasificacion --}}
          <div class="w-full px-4 py-2 flex flex-wrap gap-1 justify-start items-start">
            @foreach ($product->tags as $tag)
              <span class="py-0.5 px-1 rounded-md text-xs uppercase text-orange-800 bg-orange-200">{{ $tag->tag_name }}</span>
            @endforeach
          </div>
          {{-- precio y botones --}}
          <div class="w-full px-4 flex flex-col justify-between mb-auto">
            {{-- precios --}}
            <div class="w-full space-y-1">
              @foreach ($product->prices as $price)
                <div class="relative flex justify-between items-center text-md text-neutral-700">
                  <span class="capitalize font-semibold">{{ $price->description }}({{ $price->quantity }})</span>
                  <span class="">${{ number_format($price->price, 2) }}</span>
                  @if ($price->id === $product->defaultPrice()->id)
                    <span class="absolute left-2 -bottom-2 text-xs text-emerald-600">precio sugerido!</span>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
          {{-- boton agregar al carrito--}}
          <div class="w-full p-4 flex justify-end items-center mt-2">
            {{-- boton --}}
            @if ($product->getTotalStockAttribute() !== 0)
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
            @endif
          </div>
        </article>
      @empty
        <article class="mt-24 col-start-1 col-span-full">
          <span class="text-2xl text-orange-900">¡Vaya! no encontramos productos para esta busqueda. Prueba usando otros filtros.</span>
        </article>
      @endforelse
    </section>

    {{-- modal del carrito --}}
    @if ($show_cart_modal)
      <div class="fixed inset-0 z-40 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
          <div class="fixed inset-0 bg-neutral-900 opacity-40"></div> {{-- fondo negro --}}
          <div class="relative bg-white rounded-lg w-full max-w-5xl p-6">

            {{-- encabezado --}}
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl text-neutral-700 font-semibold">Carrito de Compras</h2>
              <button
                wire:click="hideCartModal()"
                class="inline-flex justify-between items-center mt-auto py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                >continuar comprando
                <span class="text-orange-800">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                </span>
              </button>
            </div>

            {{-- seccion de errores --}}
            <div class="flex flex-col gap-1 mb-2 border-b border-neutral-200">
              {{-- error general --}}
              @error('products_for_cart')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror

              {{-- errores de cantidades por producto --}}
              @foreach($products_for_cart as $index => $product)
                @error("products_for_cart.{$index}.order_quantity")
                  <span class="block text-red-400 text-sm">{{ $message }}</span>
                @enderror
              @endforeach

              {{-- otros errores --}}
              @foreach($errors->all() as $error)
                @if(str_contains($error, 'products_for_cart'))
                  <span class="block text-red-400 text-sm">{{ $error }}</span>
                @endif
              @endforeach
            </div>

            {{-- contenido --}}
            <div class="space-y-4 max-h-64 overflow-y-auto overflow-x-auto">
              {{-- items --}}
              @forelse($products_for_cart as $key => $item)
                {{-- item del carrito --}}
                <div wire:key="{{ $key }}" class="flex flex-col items-center justify-between border-b pb-2">
                  <div class="flex flex-col w-full relative">
                    {{-- fila item --}}
                    <div class="w-full flex justify-start items-center gap-2">
                      {{-- imagen, nombre y stock --}}
                      <div class="flex items-center space-x-4">
                        <img
                          src="{{ Storage::url($item['product']->product_image_path) }}"
                          alt="{{ $item['product']->product_name }}"
                          class="w-12 h-12 object-cover rounded"
                        />
                        <div class="flex flex-col">
                          <h3 class="font-semibold capitalize text-neutral-800">{{ $item['product']->product_name }}</h3>
                          <p class="font-semibold uppercase text-xs text-emerald-600">{{ $item['product']->getTotalStockAttribute() }} unidades disponibles!</p>
                        </div>
                      </div>
                      {{-- seleccion del precio y cantidad --}}
                      {{-- seleccion del precio --}}
                      <div class="flex flex-col">
                        <label for="" class="text-sm font-semibold text-neutral-800 mb-1">Elija un precio, combo, u oferta</label>
                        <select
                          wire:model="products_for_cart.{{ $key }}.selected_price_id"
                          wire:change="updateSelectedPrice({{ $key }}, $event.target.value)"
                          class="p-1 pr-8 rounded-md bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
                          >
                          @foreach ($item['product']->prices as $price)
                            <option value="{{ $price->id }}">
                              {{ $price->description }} ({{ $price->quantity }} unidad/es) - ${{ number_format($price->price, 2) }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      {{-- cantidad --}}
                      {{-- input cantidad --}}
                      <div class="flex flex-col">
                        <label for="" class="text-sm font-semibold text-neutral-800 mb-1">Cantidad a comprar</label>
                        <input
                          id="products_for_cart_{{ $key }}_order_quantity"
                          wire:model.live="products_for_cart.{{ $key }}.order_quantity"
                          type="number"
                          min="1"
                          max="{{ $item['product']->getTotalStockAttribute() }}"
                          class="w-36 p-1 text-right rounded-md bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
                        />
                      </div>
                      {{-- precio subtotal --}}
                      <div class="flex items-center space-x-4 ml-auto">
                        {{-- precio --}}
                        <p class="font-semibold">${{ number_format($item['subtotal_price'], 2) }}</p>
                        {{-- papelera individual --}}
                        <button
                          wire:click="removeProductForSale({{ $key }})"
                          class="text-red-500 hover:text-red-700">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="flex items-center justify-start">
                  <p class="text-center text-gray-600">¡El carrito está vacío!</p>
                </div>
              @endforelse
            </div>

            {{-- opciones del carrito --}}
            <div class="mt-2">
              @if ($products_for_cart->isNotEmpty())
                {{-- calculo del total del carrito --}}
                <div class="flex justify-end items-center pt-4">
                  <p class="text-2xl font-bold text-orange-800">Total:&nbsp;${{ number_format($total_for_sale, 2) }}</p>
                </div>
                {{-- botones de carrito y pago --}}
                <div class="flex justify-between items-center mt-2">
                  {{-- Botón vaciar carrito --}}
                  <button
                      wire:click="resetCart()"
                      class="inline-flex justify-between items-center mt-auto py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                      >vaciar carrito
                      <span class="text-red-500">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                      </span>
                  </button>
                  {{-- boton proceder al pedido --}}
                  <a
                    href="#"
                    wire:click="showConfirmationModal()"
                    class="inline-flex justify-between items-center mt-auto py-1 px-2 rounded border-2 border-orange-950 bg-orange-800 text-orange-100"
                    >hacer pedido
                    <span class="text-orange-100 ml-1">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </span>
                  </a>
                </div>
              @endif
            </div>

          </div>
        </div>
      </div>
    @endif

    {{-- modal de confirmacion --}}
    @if ($show_confirmation_modal)
      <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
          <div class="fixed inset-0 bg-neutral-900 opacity-40"></div> {{-- fondo negro --}}
          <div class="relative bg-white rounded-lg w-full max-w-md p-6">
            <div class="mb-4 pb-2 space-y-2 border-b border-neutral-200">
              <span class="font-semibold text-orange-800 text-2xl">¿Hacer el pedido?</span>
              <p class="">Por favor confirme si desea hacer el pedido de los productos del carrito. Una vez confirmado, se continuará con el pago.</p>
            </div>
            <div class="flex justify-between items-center">
              {{-- cancelar --}}
              <button
                wire:click="closeConfirmationModal()"
                class="inline-flex justify-between items-center mt-auto py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                >cancelar
                <span class="text-orange-500">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </span>
              </button>
              {{-- boton proceder al pedido --}}
              <a
                href="#"
                wire:click="proceedToCheckout()"
                class="inline-flex justify-between items-center mt-auto py-1 px-2 rounded border-2 border-orange-950 bg-orange-800 text-orange-100"
                >confirmar
                <span class="text-orange-100 ml-1">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </span>
              </a>
            </div>
          </div>
        </div>
      </div>
    @endif

  </main>

  <div role="paginacion" class="max-w-5xl mx-auto">
    <div class="p-10">
      {{ $products->links() }}
    </div>
  </div>
</div>
