<div wire:poll class="mt-20 pt-5 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 h-screen">
  {{-- componente de mis pedidos --}}
  <div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-6xl mx-auto p-6 h-4/5">

    <div class="w-full">
      {{-- cabecera --}}
      <div class="flex justify-start items-center mb-4 gap-2">
        {{-- titulo de seccion --}}
        <h2 class="text-xl text-neutral-700 font-semibold">Mis pedidos</h2>
      </div>

      {{-- seccion de busqueda --}}
      <div class="flex flex-col md:flex-row gap-4 mb-4">
        <div class="w-full md:w-1/2">
          <label for="search_order" class="block text-sm font-medium text-neutral-700 mb-1">
            Buscar por código de orden
          </label>
          <input
            type="text"
            id="search_order"
            wire:model.live="search_order"
            class="rounded-md w-full py-1 px-2 text-sm bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
            placeholder="Buscar..."
          />
        </div>

        <div class="w-full md:w-1/2">
          <label for="search_order_date" class="block text-sm font-medium text-neutral-700 mb-1">
            Buscar por fecha de pedido
          </label>
          <input
            type="date"
            id="search_order_date"
            wire:model.live="search_order_date"
            class="rounded-md w-full py-1 px-2 text-sm bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
          />
        </div>
      </div>
      <div class="flex flex-col md:flex-row gap-4 mb-4">
        <div class="w-full md:w-1/2">
          <label for="search_payment_status" class="block text-sm font-medium text-neutral-700 mb-1">
            Filtrar por estado de pago
          </label>
          <select
            id="search_payment_status"
            wire:model.live="search_payment_status"
            class="rounded-md w-full py-1 px-2 text-sm bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
          >
            <option value="">Todos</option>
            <option value="{{ $order_payment_status_pendiente }}">Pendiente</option>
            <option value="{{ $order_payment_status_aprobado }}">Aprobado</option>
            <option value="{{ $order_payment_status_rechazado }}">Rechazado</option>
          </select>
        </div>

        <div class="w-full md:w-1/2">
          <label for="search_order_status" class="block text-sm font-medium text-neutral-700 mb-1">
            Filtrar por estado de pedido
          </label>
          <select
            id="search_order_status"
            wire:model.live="search_order_status"
            class="rounded-md w-full py-1 px-2 text-sm bg-orange-100 text-orange-800 font-light border-orange-600 focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
          >
            <option value="">Todos</option>
            <option value="{{ $order_status_pendiente }}">Pendiente</option>
            <option value="{{ $order_status_entregado }}">Entregado</option>
            <option value="{{ $order_status_cancelado }}">Cancelado</option>
          </select>
        </div>
      </div>

      {{-- tabla de pedidos --}}
      <div class="w-full">
        <table class="w-full border border-collapse">
          <thead class="bg-orange-100">
            <tr class="text-neutral-800 capitalize">
              <th class="border p-1 text-left">
                código
              </th>
              <th class="border p-1 text-left">
                estado del pago
              </th>
              <th class="border p-1 text-right">
                $&nbsp;total
              </th>
              <th class="border p-1 text-right">
                fecha de pedido
              </th>
              <th class="border p-1 text-left">
                estado del pedido
                <x-quest-icon title="indica en que estado esta el pedido, pendiente de entrega, entregado o cancelado" />
              </th>
              <th class="border p-1 text-left">
                acciones
              </th>
            </tr>
          </thead>
          <tbody>
            @forelse ($orders as $order)
              <tr wire:key="{{ $order->id}}" class="text-neutral-600">
                <td class="border p-1 text-left text-xs uppercase">
                  {{ $order->order_code }}
                </td>
                <td class="border p-1 text-left">
                  @if ($order->payment_status === $order_payment_status_pendiente)
                    <x-text-tag color="orange">{{ $order->payment_status }}</x-text-tag>
                  @elseif ($order->payment_status === $order_payment_status_aprobado)
                    <x-text-tag color="emerald">{{ $order->payment_status }}</x-text-tag>
                  @else
                    <x-text-tag color="red">{{ $order->payment_status }}</x-text-tag>
                  @endif
                </td>
                <td class="border p-1 text-right">
                  $&nbsp;{{ number_format($order->total_price, 2) }}
                </td>
                <td class="border p-1 text-right">
                  {{ formatDateTime($order->created_at, 'd-m-Y') }}
                </td>
                <td class="border p-1 text-left">
                  @if ($order->status->id === $order_status_pendiente)
                    <x-text-tag color="orange">entrega&nbsp;{{ $order->status->status }}</x-text-tag>
                  @elseif ($order->status->id === $order_status_entregado)
                    <x-text-tag color="emerald">entrega&nbsp;{{ $order->status->status }}</x-text-tag>
                  @else
                    <x-text-tag color="red">entrega&nbsp;{{ $order->status->status}}</x-text-tag>
                  @endif
                </td>
                <td class="border p-1 text-start">
                  <button
                    type="button"
                    wire:click="showDetails({{ $order->id }})"
                    class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                    >ver pedido
                  </button>
                  {{-- pago recibido --}}
                  @if ($order->payment_status === $order_payment_status_aprobado)
                    <button
                      type="button"
                      wire:click="showPayment({{ $order->id}})"
                      class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                      >comprobante
                    </button>
                  @endif
                  {{-- aun no se confirma el pago o no realizo pago --}}
                  @if ($order->payment_status === $order_payment_status_pendiente)
                    <button
                      type="button"
                      wire:click="redirectToPay({{ $order->id }})"
                      class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                      >pagar
                    </button>
                    <button
                      type="button"
                      wire:click="showCancelOrderModal({{ $order->id }})"
                      class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                      >cancelar pedido
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr class="">
                <td colspan="6" class="border p-1 text-left text-neutral-600 capitalize">¡sin registros!</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="w-full mt-6">
        {{-- paginacion --}}
        {{ $orders->links() }}
      </div>

      {{-- modal: detalles del pedido, productos --}}
      <div
        x-data="{ show: @entangle('show_details_modal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">

          <div class="fixed inset-0 bg-neutral-950 opacity-40"></div>

          <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl text-neutral-700 font-semibold">Detalles del pedido</h2>
              <button
                wire:click="closeDetails()"
                class="inline-flex justify-between items-center mt-auto py-1 px-2 text-sm rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                >cerrar
              </button>
            </div>

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
                  <span class="text-2xl font-bold text-orange-800">Total:&nbsp;${{ number_format($details_order->total_price, 2) }}</span>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- modal: detalles del pago --}}
      @if ($show_payment_modal && $payment_order && $payment_order->sale)
        <div class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
            <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl text-neutral-700 font-semibold">detalles del pago:</h2>
                <button
                  wire:click="closePayment()"
                  class="inline-flex justify-between items-center mt-auto py-1 px-2 text-sm rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                  >cerrar
                </button>
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

      {{-- modal para cncelar pedido --}}
      @if ($show_cancel_modal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
            <div class="relative bg-white rounded-lg w-full max-w-md p-6">
              <div class="mb-4 pb-2 space-y-2 border-b border-neutral-200">
                <span class="font-semibold text-orange-800 text-2xl">¿Cancelar el pedido?</span>
                <p class="">Por favor confirme si desea cancelar el pedido. Esta accion es irreversible!</p>
              </div>
              <div class="flex justify-between items-center">
                {{-- cancelar --}}
                <button
                  wire:click="closeCancelOrderModal()"
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
                  wire:click="cancelOrder({{ $cancel_order }})"
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

    </div>

  </div>
</div>
