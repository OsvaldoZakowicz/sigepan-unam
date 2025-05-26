<div wire:poll>
  {{-- componente listar pedidos --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de pedidos">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="">buscar pedido</label>
            <input
            type="text"
            id="search_order"
            wire:model.live="search_order"
            class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            placeholder="Buscar por codigo de orden o cliente"
          />
          </div>

          {{-- fecha de pedido desde --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_start_at">fecha de pedido desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- fecha de pedido hasta --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_end_at">fecha de pedido hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- estado del pago --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_payment_status">Filtrar por estado de pago</label>
            <select
              id="search_payment_status"
              wire:model.live="search_payment_status"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            >
              <option value="">Todos</option>
              <option value="{{ $order_payment_status_pendiente }}">Pendiente</option>
              <option value="{{ $order_payment_status_aprobado }}">Aprobado</option>
              <option value="{{ $order_payment_status_rechazado }}">Rechazado</option>
            </select>
          </div>

          {{-- estado del pedido --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_order_status">Filtrar por estado de pedido</label>
            <select
              id="search_order_status"
              wire:model.live="search_order_status"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            >
              <option value="">Todos</option>
              <option value="{{ $order_status_pendiente }}">Pendiente</option>
              <option value="{{ $order_status_entregado }}">Entregado</option>
              <option value="{{ $order_status_cancelado }}">Cancelado</option>
            </select>
          </div>

        </div>

        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>

      </x-slot:header>

      <x-slot:content>

        {{-- tabla de pedidos recibidos --}}
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">
                id
              </x-table-th>
              <x-table-th class="text-start">
                codigo de pedido
              </x-table-th>
              <x-table-th class="text-start">
                cliente
                <x-quest-icon title="nombre del cliente que ordenó el pedido" />
              </x-table-th>
              <x-table-th class="text-start">
                estado del pago
                <x-quest-icon title="indica si el cliente registró o no un pago, o si cancelo el pago al cancelar el pedido" />
              </x-table-th>
              <x-table-th class="text-end">
                $total
              </x-table-th>
              <x-table-th class="text-start">
                fecha de pedido
              </x-table-th>
              <x-table-th class="text-start">
                estado del pedido
                <x-quest-icon title="indica en que estado esta la entrega de los productos pedidos" />
              </x-table-th>
              <x-table-th class="text-start">
                acciones
                <x-quest-icon title="puede ver el detalle del pedido, marcar la entrega del producto, cancelar un pedido por falta de pago y ver el comprobante de pago" />
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($orders as $order)
              <tr class="border" wire:key="{{ $order->id }}">
                <x-table-td class="text-end">
                  {{ $order->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  <span class="text-xs uppercase">{{ $order->order_code }}</span>
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $order->user->name }}
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($order->payment_status === $order_payment_status_pendiente)
                    <x-text-tag color="orange">{{ $order->payment_status }}</x-text-tag>
                  @elseif ($order->payment_status === $order_payment_status_aprobado)
                    <x-text-tag color="emerald">{{ $order->payment_status }}</x-text-tag>
                  @else
                    <x-text-tag color="red">{{ $order->payment_status }}</x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  ${{ number_format($order->total_price, 2) }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $order->ordered_at->format('d-m-Y H:i') }} hs.
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($order->status->id === $order_status_pendiente)
                    <x-text-tag color="orange">pedido&nbsp;{{ $order->status->status }}</x-text-tag>
                  @elseif ($order->status->id === $order_status_entregado)
                    <x-text-tag color="emerald">pedido&nbsp;{{ $order->status->status }}</x-text-tag>
                  @else
                    <x-text-tag color="red">pedido&nbsp;{{ $order->status->status}}</x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="flex gap-1">
                    <x-a-button
                      href="#"
                      wire:click="showDetails({{ $order->id }})"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver
                    </x-a-button>
                    @if ($order->status->id === $order_status_pendiente && $order->payment_status === $order_payment_status_aprobado)
                      <x-a-button
                        href="#"
                        wire:click="showEntregarOrderModal({{ $order->id }})"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >marcar entrega
                      </x-a-button>
                    @endif
                    @if ($order->sale()->exists())
                      <x-a-button
                        href="#"
                        wire:click="showPayment({{ $order->id }})"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >comprobante
                      </x-a-button>
                    @endif
                    @if ($order->payment_status === $order_payment_status_pendiente && $order->status->id === $order_status_pendiente)
                      <x-a-button
                        href="#"
                        wire:click="showCancelOrderModal({{ $order->id }})"
                        bg_color="red-600"
                        border_color="red-600"
                        text_color="neutral-100"
                        >cancelar pedido
                      </x-a-button>
                    @endif
                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <x-table-td colspan="7">
                  <span>¡sin pedidos registrados!</span>
                </x-table-td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- modal: detalles del pedido, productos --}}
        @if ($show_details_modal && $details_order)
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
              <div class="fixed inset-0 bg-neutral-950 opacity-40"></div>
              <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                  <h2 class="text-xl text-neutral-700 font-semibold">Detalles del pedido</h2>
                  <x-a-button
                    href="#"
                    wire:click="closeDetails()"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >cerrar
                  </x-a-button>
                </div>
                {{-- cliente --}}
                <div class="flex flex-col justify-start items-start mb-2 gap-1">
                  @if ($details_order->user->profile()->exists())
                    <span>
                      <span class="font-semibold">Cliente:&nbsp;</span>
                      <span class="capitalize">{{ $details_order->user->name }} - </span>
                      <span>{{ $details_order->user->profile->dni ? 'DNI: ' . $details_order->user->profile->dni : ''  }}</span>
                    </span>
                    <span>
                      <span class="font-semibold">Contacto:&nbsp;</span>
                      <span>{{ $details_order->user->profile->phone_number ? 'Tel: ' . $details_order->user->profile->phone_number : '' }} - </span>
                      <span>{{ 'Email: ' . $details_order->user->email}}</span>
                    </span>
                  @else
                    <span>
                      <span class="font-semibold">Cliente:&nbsp;</span>
                      <span class="capitalize">{{ $details_order->user->name }} - </span>
                      <span>{{ 'Email: ' . $details_order->user->email }}</span>
                    </span>
                  @endif
                </div>
                {{-- pedido --}}
                <div class="space-y-4">
                  @if ($details_order && $details_order->products)
                    @foreach ($details_order->products as $product)
                      <div wire:key="{{ $loop->index }}" class="flex flex-col items-center justify-between border-b pb-2">
                        {{-- producto --}}
                        <div class="w-full flex justify-between items-center">
                          {{-- imagen y precio unitario --}}
                          <div class="flex items-center gap-2">
                            <img
                              src="{{ Storage::url($product->product_image_path) }}"
                              alt="{{ $product->product_name }}"
                              class="w-16 h-16 object-cover rounded"
                            />
                            <div>
                              <h3 class="font-semibold">{{ $product->product_name }}</h3>
                              <p class="text-gray-500">${{ $product->pivot->details }}</p>
                            </div>
                          </div>
                          {{-- cantidad --}}
                          <div class="flex items-center justify-end w-1/6">
                            <span class="font-semibold">cantidad:&nbsp;</span>
                            <span>{{ $product->pivot->order_quantity }}</span>
                          </div>
                          {{-- precio --}}
                          <div class="flex items-center justify-end w-1/6">
                            <span class="font-semibold">Subtotal:&nbsp;</span>
                            <span>${{ number_format($product->pivot->subtotal_price, 2) }}</span>
                          </div>
                        </div>
                      </div>
                    @endforeach
                    {{-- total --}}
                    <div class="flex justify-end items-center w-full p-6">
                      <span class="text-xl font-bold">Total:&nbsp;${{ number_format($details_order->total_price, 2) }}</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endif

        {{-- modal: detalles del pago --}}
        @if ($show_payment_modal && $payment_order && $payment_order->sale)
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
              <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
              <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                  <h2 class="text-xl text-neutral-700 font-semibold">detalles del pago:</h2>
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
                @if ($payment_order->sale->sale_type === $sale_type_web)
                  @php
                    $sale_payment_data = json_decode($payment_order->sale->full_response, true);
                  @endphp
                  {{-- si se trata de comprobante de mercado pago --}}
                  @if (count($sale_payment_data['mp']) !== 0)
                    <div class="space-y-4">
                      <div class="flex flex-col justify-start items-start gap-1">
                        <span>
                          <span class="font-semibold">Orden:&nbsp;</span>
                          <span class="text-sm uppercase">{{ $payment_order->order_code }}</span>
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
                          <span>{{ $payment_order->sale->sold_on->format('d-m-Y H:i') }} hs.</span>
                        </span>
                        <span>
                          <span class="font-bold">monto:</span>
                          <span>${{ number_format($payment_order->sale->total_price, 2) }}</span>
                        </span>
                      </div>
                    </div>
                  @endif
                @endif
              </div>
            </div>
          </div>
        @endif

        {{-- modal para cancelar pedido --}}
        @if ($show_cancel_modal)
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
              <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
              <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                <div class="mb-4 pb-2 space-y-2 border-b border-neutral-200">
                  <span class="font-semibold text-neutral-800 text-2xl">¿Cancelar el pedido?</span>
                  <p class="">Por favor confirme si desea cancelar el pedido. Esta accion es irreversible!</p>
                </div>
                <div class="flex justify-between items-center">
                  {{-- cancelar --}}
                  <x-a-button
                    href="#"
                    wire:click="closeCancelOrderModal()"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >cancelar
                  </x-a-button>
                  {{-- boton proceder a cancelar --}}
                  <x-a-button
                    href="#"
                    wire:click="cancelOrder({{ $cancel_order }})"
                    bg_color="red-600"
                    border_color="red-600"
                    text_color="neutral-100"
                    >confirmar
                  </x-a-button>
                </div>
              </div>
            </div>
          </div>
        @endif

        {{-- modal para entregar pedido --}}
        @if ($show_entrega_modal)
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
              <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
              <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                <div class="mb-4 pb-2 space-y-2 border-b border-neutral-200">
                  <span class="font-semibold text-neutral-800 text-2xl">¿Entregar el pedido?</span>
                  <p class="">Por favor confirme si desea marcar la entrega de los productos del pedido. Solo puede entregar los productos cuando el pedido tiene el pago aprobado. Esta accion es irreversible!</p>
                </div>
                <div class="flex justify-between items-center">
                  {{-- cancelar --}}
                  <x-a-button
                    href="#"
                    wire:click="closeEntregarOrderModal()"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >cancelar
                  </x-a-button>
                  {{-- boton proceder al la entrega --}}
                  <x-a-button
                    href="#"
                    wire:click="entregaOrder({{ $order_to_entrega }})"
                    bg_color="emerald-600"
                    border_color="emerald-600"
                    text_color="neutral-100"
                    >confirmar
                  </x-a-button>
                </div>
              </div>
            </div>
          </div>
        @endif

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- {{ $sales->links() }} --}}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
