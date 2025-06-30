<div wire:poll class="h-screen pt-5 mt-20 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900">
  {{-- componente vista de las compras del cliente --}}
  <div class="flex items-start justify-between w-full max-w-6xl gap-8 p-6 mx-auto bg-white rounded-lg h-4/5">

    <div class="w-full">
      {{-- cabecera --}}
      <div class="flex items-center justify-start gap-2 mb-4">
        {{-- titulo de seccion --}}
        <h2 class="text-xl font-semibold text-neutral-700">Mis compras</h2>
      </div>

      {{-- seccion de busqueda --}}
      <div class="flex flex-col gap-4 mb-4 md:flex-row">
        <div class="w-full md:w-1/2">
          <label for="search_purchase" class="block mb-1 text-sm font-medium text-neutral-700">
            Buscar por ID, tipo de compra, forma de pago o código de orden
          </label>
          <input
            type="text"
            id="search_purchase"
            wire:model.live="search_purchase"
            class="w-full px-2 py-1 text-sm font-light text-orange-800 bg-orange-100 border-orange-600 rounded-md focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
            placeholder="Buscar..."
          />
        </div>

        <div class="w-full md:w-1/2">
          <label for="search_purchase_date" class="block mb-1 text-sm font-medium text-neutral-700">
            Buscar por fecha de compra
          </label>
          <input
            type="date"
            id="search_purchase_date"
            wire:model.live="search_purchase_date"
            class="w-full px-2 py-1 text-sm font-light text-orange-800 bg-orange-100 border-orange-600 rounded-md focus:ring-orange-800 focus:border-orange-800 focus:outline-none"
          />
        </div>
      </div>

      {{-- tabla de pedidos --}}
      <div class="w-full overflow-x-auto overflow-y-auto max-h-72">
        <table class="w-full border border-collapse">
          <thead class="bg-orange-100">
            <tr class="capitalize text-neutral-800">
              <th class="p-1 text-left border">
                id
              </th>
              <th class="p-1 text-left border">
                tipo de compra
                <x-quest-icon title="indica si fue una compra mediante un pedido online o presencial en el local" />
              </th>
              <th class="p-1 text-left border">
                forma de pago
              </th>
              <th class="p-1 text-right border">
                $&nbsp;total
              </th>
              <th class="p-1 text-right border">
                fecha de compra
              </th>
              <th class="p-1 text-left border">
                acciones
              </th>
            </tr>
          </thead>
          <tbody>
            @forelse ($sales as $sale)
              <tr wire:key="{{ $sale->id}}" class="text-neutral-600">
                <td class="p-1 text-left border">
                  {{ $sale->id }}
                </td>
                <td class="p-1 text-left border">
                  {{ $sale->sale_type }}
                  @if ($sale->order()->exists())
                    <span class="text-xs uppercase">({{ $sale->order->order_code }})</span>
                  @endif
                </td>
                <td class="p-1 text-left border">
                  {{ $sale->payment_type }}
                </td>
                <td class="p-1 text-right border">
                  ${{ number_format($sale->total_price, 2) }}
                </td>
                <td class="p-1 text-right border">
                  {{ $sale->sold_on->format('d-m-Y H:i') }} hs.
                </td>
                <td class="p-1 text-left border">
                  <button
                    type="button"
                    wire:click="showPayment({{ $sale->id }})"
                    class="inline-flex items-center justify-between px-2 py-1 text-xs text-orange-800 bg-orange-200 border-2 rounded border-orange-950"
                    >ver comprobante
                  </button>
                </td>
              </tr>
            @empty
              <tr class="">
                <td colspan="6" class="p-1 text-left capitalize border text-neutral-600">¡sin registros!</td>
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
            <div class="relative w-full max-w-3xl p-6 bg-white rounded-lg">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-neutral-700">detalles del pago:</h2>
                <button
                  wire:click="closePayment()"
                  class="inline-flex items-center justify-between px-2 py-1 mt-auto text-sm text-orange-800 bg-orange-200 border-2 rounded border-orange-950"
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
                    <div class="flex flex-col items-start justify-start gap-1">
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
                  <header class="p-1 mb-2 border border-neutral-100">
                    <div class="flex items-center justify-between">
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
