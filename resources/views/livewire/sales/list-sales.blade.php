<div>
  {{-- componente listar ventas --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de ventas">

      <x-a-button
        href="#"
        wire:click="openNewSaleModal()"
        class="mx-1"
        >registrar ventas
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex items-end justify-start gap-1">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-56">
            <label for="">buscar venta</label>
            <input
              type="text"
              name="search_sale"
              wire:model.live="search_sale"
              wire:click="resetPagination()"
              placeholder="ingrese un id de venta, o cliente"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- fecha de venta desde --}}
          <div class="flex flex-col justify-end w-56">
            <label for="search_start_at">fecha de venta desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              wire:click="resetPagination()"
              class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- fecha de venta hasta --}}
          <div class="flex flex-col justify-end w-56">
            <label for="search_end_at">fecha de venta hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              wire:click="resetPagination()"
              class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- limpiar campos de busqueda --}}
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar filtros
          </x-a-button>
        </div>

      </x-slot:header>

      <x-slot:content>

        {{-- tabla de ventas realizadas --}}
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-12 text-end">
                id
              </x-table-th>
              <x-table-th class="text-start">
                tipo de venta
                <x-quest-icon title="venta realizada en el local o registrada en la tienda online" />
              </x-table-th>
              <x-table-th class="text-start">
                forma de pago
                <x-quest-icon title="forma de pago que uso el cliente" />
              </x-table-th>
              <x-table-th class="text-start">
                cliente
                <x-quest-icon title="clientes registrados o no en la tienda online" />
              </x-table-th>
              <x-table-th class="text-end">
                $total
              </x-table-th>
              <x-table-th class="text-start">
                estado
                <x-quest-icon title="indica si los productos de la venta fueron entregados o estan pendientes" />
              </x-table-th>
              <x-table-th class="text-end">
                fecha de venta
              </x-table-th>
              <x-table-th class="w-48 text-start">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($sales as $sale)
              <tr class="border" wire:key="{{ $sale->id }}">
                <x-table-td class="text-end">
                  {{ $sale->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $sale->sale_type }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- todo: si es mp, mostrar datos de el pago --}}
                  {{ $sale->payment_type }}
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($sale->user)
                    <span class="capitalize">{{ $sale->user->name }}</span>
                  @else
                    <span class="text-neutral-400">{{ $sale->client_type }}</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  ${{ number_format($sale->total_price, 2) }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- debe existir la orden --}}
                  @if ($sale->order()->exists())
                    @if ($sale->order->status->id === $order_status_pendiente)
                      <x-text-tag color="orange">pedido&nbsp;{{ $sale->order->status->status }}</x-text-tag>
                    @elseif ($sale->order->status->id === $order_status_entregado)
                      <x-text-tag color="emerald">pedido&nbsp;{{ $sale->order->status->status }}</x-text-tag>
                    @else
                      <x-text-tag color="red">pedido&nbsp;{{ $sale->order->status->status}}</x-text-tag>
                    @endif
                  @else
                    {{-- venta directa --}}
                    <x-text-tag color="emerald">pedido entregado</x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $sale->sold_on->format('d-m-Y H:i') }} hs.
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="flex gap-1">

                    <x-a-button
                      href="#"
                      wire:click="openShowSaleModal({{ $sale }})"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver
                    </x-a-button>

                    <x-a-button
                      href="#"
                      wire:click="showPayment({{ $sale->id }})"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >comprobante
                    </x-a-button>

                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <x-table-td colspan="6">
                  <span>¡sin ventas ralizadas!</span>
                </x-table-td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- modal de registro de ventas --}}
        @if ($show_new_sale_modal)
          <div class="fixed inset-0 z-50 flex items-center justify-center w-full h-full overflow-y-auto bg-neutral-400 bg-opacity-40">
            <div class="w-5/6 p-5 transition-all transform bg-white border rounded-md shadow-lg">
              <div class="text-start">

                {{-- titulo del modal --}}
                <h3 class="text-lg font-medium leading-6 capitalize text-neutral-800">
                  Nueva venta
                </h3>

                {{-- seccion de cliente --}}
                <x-div-toggle
                  x-data="{ open: false }"
                  title="cliente"
                  subtitle="añada un cliente a esta venta"
                  class="p-2 mt-2"
                  >
                  <div class="flex items-start justify-start gap-1 mb-1 grow">
                    {{-- busqueda de usuarios cliente --}}
                    <div x-data="{
                      open: false,
                      search: '',
                      selected: null
                      }" class="relative w-1/3">
                      <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-neutral-600">Buscar cliente</label>
                        <input
                          type="text"
                          x-model="search"
                          @focus="open = true"
                          @click.outside="open = false"
                          wire:model.live="user_search"
                          placeholder="Buscar por nombre o email"
                          class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                        >
                      </div>

                      <!-- Lista de resultados -->
                      <div
                        x-show="open"
                        x-transition
                        class="absolute z-50 w-full mt-1 bg-white border rounded-sm shadow-lg border-neutral-200">
                        <ul class="py-1 overflow-auto max-h-60">
                          @forelse($users as $user)
                            <li
                              wire:key="{{ $user->id }}"
                              wire:click="selectUser({{ $user->id }})"
                              @click="
                                selected = {{ $user->id }};
                                search = '{{ $user->name }}';
                                open = false;
                              "
                              class="px-3 py-2 text-sm cursor-pointer hover:bg-neutral-100"
                            >
                              {{ $user->name }} - {{ $user->email }}
                            </li>
                          @empty
                            <li class="px-3 py-2 text-sm text-neutral-500">No se encontraron resultados</li>
                          @endforelse
                        </ul>
                      </div>
                    </div>
                  </div>
                </x-div-toggle>

                {{-- seccion de busqueda de productos --}}
                <x-div-toggle
                  x-data="{ open: false }"
                  title="buscar productos"
                  subtitle="busque productos para agregarlos a la lista de ventas"
                  class="p-2 mt-1"
                  >
                  {{-- busqueda --}}
                  <div class="flex items-start justify-start gap-1 mb-1 grow">
                    <div class="flex flex-col justify-end w-1/3">
                      <input
                        type="text"
                        name="search_product"
                        wire:model.live="search_product"
                        wire:click="resetPagination()"
                        placeholder="ingrese un id, o nombre de producto"
                        class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                    </div>
                  </div>
                  {{-- resultados de busqueda --}}
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="w-12 text-end">
                          id
                        </x-table-th>
                        <x-table-th class="text-start">
                          producto
                        </x-table-th>
                        <x-table-th class="text-start">
                          $precios
                          <x-quest-icon title="precio por defecto usado y listado de todos los precios" />
                        </x-table-th>
                        <x-table-th class="text-end">
                          cantidad disponible
                          <x-quest-icon title="en unidades" />
                        </x-table-th>
                        <x-table-th class="w-12 text-end">
                          elegir
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($available_products as $product)
                        <tr class="border">
                          <x-table-td class="text-end">
                            {{ $product->id }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{ $product->product_name }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            <div class="flex w-full gap-1">
                              {{-- precio por defecto --}}
                              <span class="font-semibold">
                                <span class="text-sm">{{ $product->defaultPrice()->description }}&nbsp;({{ $product->defaultPrice()->quantity }} unidad/es)</span>
                                <span class="text-sm">${{ $product->defaultPrice()->price }}</span>
                              </span>
                              {{-- lista de precios --}}
                              <div x-data="{ open: false }" class="relative">
                                {{-- texto con desplegable de lista --}}
                                <button
                                  @click="open = !open"
                                  class="flex items-center text-blue-700 hover:text-blue-900">
                                  <span class="underline">Lista de Precios</span>
                                </button>
                                {{-- lista desplegable --}}
                                <ul x-show="open"
                                  @click.away="open = false"
                                  class="absolute z-10 p-1 mt-1 bg-white border border-gray-200 rounded-sm shadow-lg w-96">
                                  <li class="w-full p-1 font-semibold capitalize border-b text-start">lista de precios:</li>
                                  @forelse ($product->prices as $price)
                                    <li class="p-1 w-full flex justify-between hover:bg-gray-100 @if ($price->is_default) font-semibold @endif">
                                      <span class="text-sm">{{ $price->description }}&nbsp;({{ $price->quantity }} unidad/es)</span>
                                      <span class="text-sm">${{ $price->price }}</span>
                                    </li>
                                  @empty
                                    <li class="p-2 text-gray-500">¡sin precios!</li>
                                  @endforelse
                                </ul>
                              </div>
                            </div>
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ $product->getTotalStockAttribute() }}
                          </x-table-td>
                          <x-table-td class="w-12 text-end">
                            {{-- agregar a la lista --}}
                            <div class="inline-flex items-center justify-start w-full gap-1">
                              <span
                                wire:click="addProductForSale({{ $product }})"
                                title="elegir y agregar a la lista"
                                class="p-1 font-bold leading-none text-center border rounded-sm cursor-pointer bg-neutral-100 border-neutral-200"
                                >&plus;
                              </span>
                            </div>
                          </x-table-td>
                        </tr>
                      @empty
                        <tr class="border">
                          <x-table-td colspan="4" class="text-start">
                            <span>¡sin productos disponibles!</span>
                          </x-table-td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                  <div class="mt-1">
                    {{ $available_products->links() }}
                  </div>
                </x-div-toggle>

                {{-- seccion de lista de compras --}}
                <div class="mt-4">
                  {{-- Lista de mensajes --}}
                  <div class="mb-1">
                    {{-- Error general de products_for_sale --}}
                    @error('products_for_sale')
                      <span class="block mb-1 text-sm text-red-400">{{ $message }}</span>
                    @enderror

                    {{-- Errores de cantidades por producto --}}
                    @foreach($products_for_sale as $index => $product)
                      @error("products_for_sale.{$index}.sale_quantity")
                        <span class="block mb-1 text-sm text-red-400">{{ $message }}</span>
                      @enderror
                    @endforeach

                    {{-- Otros errores relacionados a products_for_sale --}}
                    @foreach($errors->all() as $error)
                      @if(str_contains($error, 'products_for_sale'))
                        <span class="block mb-1 text-sm text-red-400">{{ $error }}</span>
                      @endif
                    @endforeach

                    {{-- mensaje de informacion, producto ya en la lista --}}
                    <div
                      x-data="{ show: false, message: '' }"
                      x-on:already-on-list.window="
                        show = true;
                        message = 'El producto ya está en la lista de venta';
                        setTimeout(() => show = false, 3000)"
                      ><span x-show="show" x-transition class="block mb-1 text-sm text-blue-400" x-text="message"></span>
                    </div>
                  </div>
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="w-12 text-end">
                          Id
                        </x-table-th>
                        <x-table-th class="text-start">
                          Producto
                        </x-table-th>
                        <x-table-th class="text-start">
                          Precios
                          <x-quest-icon title="elija uno de los precios del producto"/>
                        </x-table-th>
                        <x-table-th class="text-end">
                          Cantidad a vender
                          <x-quest-icon title="indique cuanto desea comprar el cliente" />
                        </x-table-th>
                        <x-table-th class="text-end">
                          $Precio unitario
                        </x-table-th>
                        <x-table-th class="text-end">
                          $Subtotal
                        </x-table-th>
                        <x-table-th class="w-12 text-start">
                          quitar
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($products_for_sale as $key => $pfs)
                        <tr wire:key="{{ $key }}" class="border">
                          <x-table-td class="text-end">
                            {{ $pfs['product']->id }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{ $pfs['product']->product_name }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{-- select precios o combos --}}
                            <div class="flex items-center justify-end">
                              {{-- En la sección de precios de la tabla --}}
                              <select
                                wire:model="products_for_sale.{{ $key }}.selected_price_id"
                                wire:change="updateSelectedPrice({{ $key }}, $event.target.value)"
                                class="w-full p-1 text-sm border text-start border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                >
                                @foreach ($pfs['product']->prices as $price)
                                  <option value="{{ $price->id }}">
                                    {{ $price->description }} ({{ $price->quantity }} unidad/es) - ${{ number_format($price->price, 2) }}
                                  </option>
                                @endforeach
                              </select>
                            </div>
                          </x-table-td>
                          <x-table-td class="w-56 text-end">
                            {{-- input cantidad a vender --}}
                            <div class="flex items-center justify-end">
                              <input
                                id="products_for_sale_{{ $key }}_sale_quantity"
                                wire:model.live="products_for_sale.{{ $key }}.sale_quantity"
                                type="number"
                                min="1"
                                max="{{ $pfs['product']->getTotalStockAttribute() }}"
                                class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                              />
                            </div>
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ number_format($pfs['unit_price'], 2) }}
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ number_format($pfs['subtotal_price'], 2) }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{-- quitar de la lista --}}
                            <div class="inline-flex items-center justify-start w-full gap-1">
                              <span
                                wire:click="removeProductForSale({{ $key }})"
                                title="elegir y agregar a la lista"
                                class="p-1 font-bold leading-none text-center bg-red-100 border border-red-200 rounded-sm cursor-pointer"
                                >&times;
                              </span>
                            </div>
                          </x-table-td>
                        </tr>
                      @empty
                        <tr class="border">
                          <x-table-td colspan="6">
                            <span>¡sin productos seleccionados!</span>
                          </x-table-td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                </div>

                {{-- total --}}
                <div class="flex justify-end gap-2 mt-2">
                  <span class="text-xl font-semibold capitalize">total:</span>
                  <span class="text-xl font-semibold">${{ number_format($total_for_sale, 2) }}</span>
                </div>

                {{-- botones de venta --}}
                <div class="flex justify-end gap-2 mt-6">
                  <x-btn-button
                    color="neutral"
                    wire:click="closeNewSaleModal()"
                    >Cancelar
                  </x-btn-button>

                  <x-btn-button
                    wire:click="save()"
                    wire:confirm="¿realizar venta?"
                    >Vender
                  </x-btn-button>
                </div>
              </div>
            </div>
          </div>
        @endif

        {{-- modal de ver venta --}}
        @if ($show_sale_modal && $selected_sale)
          <div class="fixed inset-0 z-50 flex items-center justify-center w-full h-full overflow-y-auto bg-neutral-400 bg-opacity-40">
            <div class="w-3/4 p-5 transition-all transform bg-white border rounded-md shadow-lg">
              <div class="w-full text-start">
                {{-- encabezado --}}
                <header class="p-1 mb-2 border border-neutral-100">
                  <h3 class="text-lg font-semibold">Comprobante de venta</h3>
                  <small class="text-xs uppercase">documento no valido como factura</small>
                  <div class="flex flex-col gap-1">
                    <span>
                      <span class="font-semibold">Id de venta:</span>
                      {{ $selected_sale->id }}
                    </span>
                    <span>
                      <span class="font-semibold">Fecha:</span>
                      {{ $selected_sale->sold_on->format('d-m-Y H:i') }} hs.
                    </span>
                    <span>
                      <span class="font-semibold">Establecimiento:</span>
                      <span>
                        <span>{{ $details_store['razon_social'] }} - </span>
                        <span>CUIT: {{ $details_store['cuit'] }}.</span>
                      </span>
                    </span>
                    <span>
                      <span class="font-semibold">Contacto:</span>
                      <span>
                        <span>tel: {{ $details_store['telefono'] }} - </span>
                        <span>correo: {{ $details_store['email'] }}</span>
                      </span>
                    </span>
                    {{-- cliente --}}
                    <span>
                      <span class="font-semibold">Cliente: </span>
                      {{ $details_user['usuario'] }}
                    </span>
                    <span>
                      <span class="font-semibold">Perfil: </span>
                      {{ $details_user['perfil'] }}
                    </span>
                    <span>
                      <span class="font-semibold">Direccion: </span>
                      {{ $details_user['direccion'] }}
                    </span>
                    <span>
                      <span class="font-semibold">Forma de pago:</span>
                      {{ $selected_sale->payment_type }}
                    </span>
                  </div>
                </header>
                {{-- cuerpo con detalle y total --}}
                <section class="w-full mb-1 overflow-x-auto overflow-y-auto max-h-56">
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="w-12 text-end">
                          #
                        </x-table-th>
                        <x-table-th class="text-start">
                          producto
                        </x-table-th>
                        <x-table-th class="text-start">
                          detalle
                        </x-table-th>
                        <x-table-th class="text-end">
                          cantidad
                        </x-table-th>
                        <x-table-th class="text-end">
                          $precio unitario
                        </x-table-th>
                        <x-table-th class="text-end">
                          $subtotal
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @foreach ($selected_sale->products as $key => $product_sale)
                      <tr class="border" wire:key="{{ $key }}">
                        <x-table-td class="w-12 text-end">
                          {{ $key+1 }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $product_sale->product_name }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $product_sale->pivot->details }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{ $product_sale->pivot->sale_quantity }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          ${{ number_format($product_sale->pivot->unit_price, 2) }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          ${{ number_format($product_sale->pivot->subtotal_price, 2) }}
                        </x-table-td>
                      </tr>
                      @endforeach
                      <tr class="border">
                        <x-table-td class="font-semibold capitalize text-end" colspan="5">$total:</x-table-td>
                        <x-table-td class="font-semibold text-end">${{ number_format($selected_sale->total_price, 2) }}</x-table-td>
                      </tr>
                    </x-slot:tablebody>
                  </x-table-base>
                </section>
                {{-- pie con datos extra --}}
                <footer class="w-full p-1 mb-2 border border-neutral-100">
                  <div class="flex justify-end w-full">
                    <x-a-button
                      href="#"
                      wire:click="closeShowSaleModal()"
                      bg_color="neutral-600"
                      border_color="neutral-600"
                      text_color="neutral-100"
                      >cerrar
                    </x-a-button>
                  </div>
                </footer>
              </div>
            </div>
          </div>
        @endif

        {{-- modal: detalles del pago --}}
        @if ($show_payment_modal && $selected_sale_payment)
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
              <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
              <div class="relative w-full max-w-2xl p-6 bg-white rounded-lg">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold text-neutral-700">detalles del pago:</h2>
                  <x-a-button
                    href="#"
                    wire:click="closePayment()"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >cerrar
                  </x-a-button>
                </div>

                {{-- * datos si es comprobante de forma de pago virtual --}}
                @if ($selected_sale_payment->sale_type === $sale_type_web)
                  @php
                    $sale_payment_data = json_decode($selected_sale_payment->full_response, true);
                  @endphp
                  {{-- si se trata de comprobante de mercado pago --}}
                  @if (count($sale_payment_data['mp']) !== 0)
                    <div class="space-y-4">
                      <div class="flex flex-col items-start justify-start gap-1">
                        <span>
                          <span class="font-semibold">Orden:&nbsp;</span>
                          <span class="text-sm uppercase">{{ $selected_sale_payment->order->order_code }}</span>
                        </span>
                        <span>
                          <span class="font-bold">pago vía:</span>
                          <span>Mercado Pago</span>
                        </span>
                        <span>
                          <span class="font-bold">estado del pago:</span>
                          <span>{{ __($sale_payment_data['mp']['status']) }}</span>
                        </span>
                        <span>
                          <span class="font-bold">número de operación:</span>
                          <span>{{ $sale_payment_data['mp']['payment_id'] }} (nro de comprobante de mercado pago)</span>
                        </span>
                        <span>
                          <span class="font-bold">fecha de pago:</span>
                          <span>{{ $selected_sale_payment->sold_on->format('d-m-Y H:i') }} hs.</span>
                        </span>
                        <span>
                          <span class="font-bold">monto:</span>
                          <span>${{ number_format($selected_sale_payment->total_price, 2) }}</span>
                        </span>
                      </div>
                    </div>
                  @endif
                @endif
              </div>
            </div>
          </div>
        @endif

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $sales->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>

  {{-- manejar eventos --}}
  <script>

    /* evento: abrir pdf en nueva pestaña para visualizar */
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('openPdfInNewTab', ({ url }) => {
            window.open(url, '_blank');
        });
    });

  </script>
</div>
