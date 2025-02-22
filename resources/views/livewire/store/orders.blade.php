<div class="mt-24 pt-5 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 h-screen">
  {{-- componente de mis pedidos --}}
  <div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-6xl mx-auto p-6 h-4/5">

    <div class="w-full">
      {{-- cabecera --}}
      <div class="flex justify-between items-center mb-4">
        {{-- titulo de seccion --}}
        <h2 class="text-xl text-neutral-700 font-semibold">Mis pedidos</h2>
      </div>

      {{-- tabla de pedidos --}}
      <div class="w-full">
        <table class="w-full border border-collapse">
          <thead class="bg-orange-100">
            <tr class="h-12">
              <th class="border p-1 text-left text-neutral-800 capitalize">código</th>
              <th class="border p-1 text-left text-neutral-800 capitalize">estado</th>
              <th class="border p-1 text-left text-neutral-800 capitalize">pago</th>
              <th class="border p-1 text-right text-neutral-800 capitalize">$&nbsp;total</th>
              <th class="border p-1 text-right text-neutral-800 capitalize">fecha de pedido</th>
              <th class="border p-1 text-right text-neutral-800 capitalize">fecha de entrega</th>
              <th class="border p-1 text-center text-neutral-800 capitalize">acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($orders as $order)
              <tr wire:key="{{ $order->id}}" class="h-12">
                <td class="border p-1 text-left text-neutral-600 uppercase">{{ $order->order_code }}</td>
                <td class="border p-1 text-left text-neutral-600 capitalize">{{ $order->status->status }}</td>
                <td class="border p-1 text-left text-neutral-600 capitalize">{{ __( $order->sale->status ) }}</td>
                <td class="border p-1 text-right text-neutral-600">$&nbsp;{{ number_format($order->total_price, 2) }}</td>
                <td class="border p-1 text-right text-neutral-600">{{ formatDateTime($order->created_at, 'd-m-Y') }}</td>
                <td class="border p-1 text-right text-neutral-600">
                  @if ($order->delivered_at != null)
                    {{ formatDateTime($order->delivered_at, 'd-m-Y') }}
                  @else
                    <span class="font-semibold text-red-600">sin entregar</span>
                  @endif
                </td>
                <td class="border p-1 text-center text-neutral-600">
                  <button
                    type="button"
                    wire:click="showDetails({{ $order->id }})"
                    class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                    >productos
                  </button>
                  <button
                    type="button"
                    wire:click="showPayment({{ $order->id}})"
                    class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                    >pago
                  </button>
                </td>
              </tr>
            @empty
              <tr class="h-12">
                <td colspan="6" class="border p-1 text-left text-neutral-600 capitalize">¡aún no has hecho un pedido!</td>
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
              <h2 class="text-xl text-neutral-700 font-semibold">detalles del pedido</h2>
              <button
                wire:click="closeDetails()"
                class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
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
                      <div class="flex items-center gap-2 w-2/3">
                        <img
                          src="{{ Storage::url($product->product_image_path) }}"
                          alt="{{ $product->product_name }}"
                          class="w-16 h-16 object-cover rounded"
                        />
                        <div>
                          <h3 class="font-semibold">{{ $product->product_name }}</h3>
                          <p class="text-gray-500">${{ number_format($product->pivot->unit_price, 2) }} c/u</p>
                        </div>
                      </div>
                      {{-- cantidad --}}
                      <div class="flex items-center justify-end w-1/6">
                        <p class="font-semibold">cantidad:&nbsp;{{ $product->pivot->quantity }}</p>
                      </div>
                      {{-- precio --}}
                      <div class="flex items-center justify-end w-1/6">
                        <p class="font-semibold">${{ number_format($product->pivot->subtotal_price, 2) }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
                {{-- total --}}
                <div class="flex justify-end items-center w-full p-6">
                  <span class="text-2xl font-bold text-orange-800">total:&nbsp;$&nbsp;{{ number_format($details_order->total_price, 2) }}</span>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- modal: detalles del pago --}}
      <div
        x-data="{ show: @entangle('show_payment_modal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">

          <div class="fixed inset-0 bg-neutral-950 opacity-40"></div>

          <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl text-neutral-700 font-semibold">detalles del pago</h2>
              <button
                wire:click="closePayment()"
                class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                >cerrar
              </button>
            </div>

            <div class="space-y-4">
              @if ($payment_order && $payment_order->sale)
                <div class="space-y-2">
                  <p><span class="font-bold">pago vía:</span> {{ $payment_order->order_origin }}</p>
                  <p><span class="font-bold">tipo:</span> {{ $payment_order->sale->payment_type }}</p>
                  <p><span class="font-bold">plataforma:</span> {{ json_decode($payment_order->sale->full_response)->site_id }}</p>
                  <p><span class="font-bold">estado del pago:</span> {{ __( $payment_order->sale->status ) }}</p>
                  <p><span class="font-bold">número de operación:</span> {{ $payment_order->sale->payment_id }}</p>
                  <p><span class="font-bold">fecha de pago:</span> {{ formatDateTime($payment_order->sale->created_at, 'd-m-Y H:i') }}</p>
                  <p><span class="font-bold">monto:</span> ${{ number_format($payment_order->sale->total_price, 2) }}</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>
