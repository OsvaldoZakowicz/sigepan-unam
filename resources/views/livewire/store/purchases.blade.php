<div wire:poll class="mt-20 pt-5 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 h-screen">
  {{-- componente vista de las compras del cliente --}}
  <div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-6xl mx-auto p-6 h-4/5">

    <div class="w-full">
      {{-- cabecera --}}
      <div class="flex justify-start items-center mb-4 gap-2">
        {{-- titulo de seccion --}}
        <h2 class="text-xl text-neutral-700 font-semibold">Mis compras en la tienda</h2>
      </div>

      {{-- tabla de pedidos --}}
      <div class="w-full">
        <table class="w-full border border-collapse">
          <thead class="bg-orange-100">
            <tr class="text-neutral-800 capitalize">
              <th class="border p-1 text-left">
                id
              </th>
              <th class="border p-1 text-left">
                tipo de compra
                <x-quest-icon title="indica si fue una compra mediante un pedido online o presencial en el local" />
              </th>
              <th class="border p-1 text-left">
                forma de pago
              </th>
              <th class="border p-1 text-right">
                $&nbsp;total
              </th>
              <th class="border p-1 text-right">
                fecha de compra
              </th>
              <th class="border p-1 text-left">
                acciones
              </th>
            </tr>
          </thead>
          <tbody>
            @forelse ($sales as $sale)
              <tr wire:key="{{ $sale->id}}" class="text-neutral-600">
                <td class="border p-1 text-left">
                  {{ $sale->id }}
                </td>
                <td class="border p-1 text-left">
                  {{ $sale->sale_type }}
                  @if ($sale->order()->exists())
                    <span class="text-xs uppercase">({{ $sale->order->order_code }})</span>
                  @endif
                </td>
                <td class="border p-1 text-left">
                  {{ $sale->payment_type }}
                </td>
                <td class="border p-1 text-right">
                  ${{ number_format($sale->total_price, 2) }}
                </td>
                <td class="border p-1 text-right">
                  {{ $sale->sold_on->format('d-m-Y H:i') }} hs.
                </td>
                <td class="border p-1 text-left">
                  <button
                    type="button"
                    wire:click="showPayment({{ $sale->id }})"
                    class="inline-flex justify-between items-center text-xs py-1 px-2 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                    >ver comprobante
                  </button>
                </td>
              </tr>
            @empty
              <tr class="">
                <td colspan="6" class="border p-1 text-left text-neutral-600 capitalize">¡aún no has hecho una compra!</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="w-full mt-6">
        {{-- paginacion --}}
        {{ $sales->links() }}
      </div>

      {{-- modal: detalles del pago --}}
      @if ($show_payment_modal && $selected_sale)
        <div class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-neutral-950 opacity-40"></div> {{-- fondo negro --}}
            <div class="relative bg-white rounded-lg w-full max-w-3xl p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl text-neutral-700 font-semibold">detalles del pago:</h2>
                <button
                  wire:click="closePayment()"
                  class="inline-flex justify-between items-center mt-auto py-1 px-2 text-sm rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
                  >cerrar
                </button>
              </div>
              {{-- * datos si es comprobante de pago virtual --}}
              @if ($selected_sale->sale_type === $sale_type_web)
                @php
                  $sale_payment_data = json_decode($selected_sale->full_response, true);
                @endphp
                {{-- si se trata de comprobante de mercado pago --}}
                @if (count($sale_payment_data['mp']) !== 0)
                  <div class="space-y-4">
                    <div class="flex flex-col justify-start items-start gap-1">
                      <span>
                        <span class="font-semibold">Orden:&nbsp;</span>
                        <span class="text-sm uppercase">{{ $selected_sale->order->order_code }}</span>
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
                        <span>{{ $selected_sale->sold_on->format('d-m-Y H:i') }} hs.</span>
                      </span>
                      <span>
                        <span class="font-bold">monto:</span>
                        <span>${{ number_format($selected_sale->total_price, 2) }}</span>
                      </span>
                    </div>
                  </div>
                @endif
              @endif
              {{-- * datos si es comprobante de pago por compra presencial --}}
              @if ($selected_sale->sale_type === $sale_type_presencial)
                <div class="w-full text-start">
                  {{-- encabezado --}}
                  <header class="border border-neutral-100 p-1 mb-2">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-semibold">Comprobante de venta</h3>
                      @if ($selected_sale->sale_pdf_path)
                        <x-a-button
                          href="#"
                          wire:click="openPdfSale({{ $selected_sale->id }})"
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >ver pdf
                        </x-a-button>
                      @endif
                    </div>
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
                        {{ $establecimiento }}
                      </span>
                      <span>
                        <span class="font-semibold">Cliente:</span>
                        @if ($selected_sale->user()->exists())
                          {{ $selected_sale->user->name . ' - ' . $selected_sale->user->email }}
                        @else
                          {{ $selected_sale->client_type }}
                        @endif
                      </span>
                      <span>
                        <span class="font-semibold">Forma de pago:</span>
                        {{ $selected_sale->payment_type }}
                      </span>
                    </div>
                  </header>
                  {{-- cuerpo con detalle y total --}}
                  <section class="w-full max-h-56 overflow-y-auto overflow-x-auto mb-1">
                    <x-table-base>
                      <x-slot:tablehead>
                        <tr class="border bg-neutral-100">
                          <x-table-th class="text-end w-12">
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
                          <x-table-td class="text-end w-12">
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
                          <x-table-td class="text-end font-semibold capitalize" colspan="5">$total:</x-table-td>
                          <x-table-td class="text-end font-semibold">${{ number_format($selected_sale->total_price, 2) }}</x-table-td>
                        </tr>
                      </x-slot:tablebody>
                    </x-table-base>
                  </section>
                  {{-- pie con datos extra --}}
                  <footer class="w-full border border-neutral-100 p-1 mb-2">
                    <div class="w-full flex justify-end">
                      {{-- nada --}}
                    </div>
                  </footer>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif

    </div>

  </div>

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
