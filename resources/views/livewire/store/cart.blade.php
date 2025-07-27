<div class="h-screen pt-5 mt-20 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900">
  {{-- componente de pedido realizado --}}
	<div class="flex items-start justify-between w-full gap-8 p-6 mx-auto bg-white rounded-lg max-w-7xl h-4/5">
    {{-- vista del pedido --}}
    <div class="w-5/6 pr-2 border-r border-neutral-200">
      {{-- cabecera --}}
      <div class="flex items-center justify-between mb-2">
        {{-- titulo de seccion --}}
        <h2 class="text-xl font-semibold text-neutral-700">Su pedido</h2>
        {{-- boton para volver --}}
        <a
          wire:navigate
          href="{{ route('store-store-index') }}"
          class="inline-flex items-center justify-between px-2 py-1 mt-auto text-orange-800 bg-orange-200 border-2 rounded text-md border-orange-950"
          >volver a la tienda
          <span class="text-orange-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </span>
        </a>
      </div>
      {{-- detalles --}}
      <div class="flex flex-col items-start justify-start gap-1 mb-1">
        <span>
          <span class="font-semibold">Codigo de pedido:&nbsp;</span>
          <span class="text-sm uppercase">{{ $order->order_code }}</span>
        </span>
        <span>
          <span class="font-semibold">Fecha de pedido:&nbsp;</span>
          <span class="">{{ $order->ordered_at->format('d-m-Y H:i') }} hs.</span>
        </span>
        <span>
          <span class="font-semibold">Pago:&nbsp;</span>
          <span class="">{{ $order->payment_status }}</span>
        </span>
      </div>
      {{-- vista de productos --}}
      <div class="p-3 space-y-4 overflow-x-hidden overflow-y-auto max-h-72">
        @foreach($order->products as $key => $product)
          {{-- item del carrito --}}
          <div wire:key="{{ $key }}" class="flex flex-col items-center justify-between pb-2 border-b">
            {{-- item --}}
            <div class="flex items-center justify-between w-full">
              {{-- imagen, nombre y stock --}}
              <div class="flex items-center gap-2">
                <img
                  src="{{ Storage::url($product->product_image_path) }}"
                  alt="{{ $product->product_name }}"
                  class="object-cover w-12 h-12 rounded"
                />
                <div>
                  <h3 class="font-semibold capitalize">{{ $product->product_name }}</h3>
                </div>
              </div>
              {{-- detalle --}}
              <div class="flex flex-col items-start justify-start">
                <span class="font-semibold capitalize">detalle:</span>
                <span>{{ $product->pivot->details }}</span>
              </div>
              {{-- cantidad --}}
              <div class="flex flex-col items-end justify-start">
                <span class="font-semibold capitalize">cantidad:</span>
                <span>{{ $product->pivot->order_quantity }}</span>
              </div>
              {{-- precio --}}
              <div class="flex flex-col items-end justify-start">
                <span class="font-semibold capitalize">$subtotal:</span>
                <span>${{ number_format($product->pivot->subtotal_price, 2) }}</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      {{-- total --}}
      <div class="flex items-center justify-end w-full p-6">
        <span class="text-2xl font-bold text-orange-800 capitalize">total:&nbsp;$&nbsp;{{ number_format($order->total_price, 2) }}</span>
      </div>
    </div>
    {{-- espacio para pagar el pedido --}}
    <div class="max-w-72">
      {{-- titulo de seccion --}}
      <h3 class="text-lg font-semibold text-neutral-700">¡Listo, registramos su pedido!</h3>
      <p class="text-lg text-neutral-700">puede realizar el pago del mismo a continuacion!</p>
      {{-- * boton MP --}}
      <div id="wallet_container"></div>
      {{-- datos extra sobre el pedido --}}
      @if ($datos_tienda)
        <div class="flex flex-col gap-2 p-2 text-sm">
          <span><span class="font-semibold text-orange-600">Horario de atencion:&nbsp;</span>{{ $datos_tienda['horario_atencion'] ?? '' }}</span>
          <span><span class="font-semibold text-orange-600">Retiro del pedido:&nbsp;</span>{{ $datos_tienda['lugar_retiro_productos'] ?? '' }}</span>
          <span><span class="font-semibold text-orange-600">Esperamos el pago del producto hasta:&nbsp;</span>{{ $datos_tienda['tiempo_espera_pago'] ?? '' }}</span>
        </div>
      @endif
    </div>

  </div>

  {{-- inicializacion del boton de MP --}}
  <script>
		document.addEventListener('DOMContentLoaded', function() {

			window.onload = function() {

				// debug consola
				console.log('DOM y scripts cargados completamente');

				const mp = new MercadoPago('APP_USR-1175ee28-0ac9-44ff-a9fe-97fb067bf07b');
				const bricksBuilder = mp.bricks();

				mp.bricks().create("wallet", "wallet_container", {
					initialization: {
							preferenceId: "{{ $preference_id }}",
							redirectMode: "blank"
					},
					customization: {
						texts: {
							valueProp: 'smart_option',
						},
					},
				});

			};
		});
  </script>
</div>
