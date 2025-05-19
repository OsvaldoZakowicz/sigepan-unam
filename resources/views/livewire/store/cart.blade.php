<div class="mt-20 pt-5 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 h-screen">
  {{-- componente de carrito final --}}
	<div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-7xl mx-auto p-6 h-4/5">
    {{-- vista del carrito --}}
    <div class="w-5/6">
      {{-- cabecera --}}
      <div class="flex justify-between items-center mb-4">
        {{-- titulo de seccion --}}
        <h2 class="text-xl text-neutral-700 font-semibold">Carrito de Compras</h2>
        {{-- boton para volver --}}
        <a
          wire:navigate
          href="{{ route('store-store-index') }}"
          class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-200 text-orange-800"
          >continuar comprando
          <span class="text-orange-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </span>
        </a>
      </div>
      {{-- vista de productos --}}
      <div class="space-y-4 max-h-72 p-3 overflow-y-auto overflow-x-hidden">
        @forelse($cart as $key => $item)
          {{-- item del carrito --}}
          <div wire:key="{{ $key }}" class="flex flex-col items-center justify-between border-b pb-2">
            {{-- item --}}
            <div class="w-full flex justify-between items-center">
              {{-- imagen, nombre y stock --}}
              <div class="flex items-center gap-2">
                <img
                  src="{{ Storage::url($item['product']->product_image_path) }}"
                  alt="{{ $item['product']->product_name }}"
                  class="w-12 h-12 object-cover rounded"
                />
                <div>
                  <h3 class="font-semibold capitalize">{{ $item['product']->product_name }}</h3>
                  <p class="font-semibold uppercase text-xs text-emerald-600">{{ $item['product']->getTotalStockAttribute() }} unidades disponibles!</p>
                </div>
              </div>
              {{-- detalle --}}
              <div class="flex flex-col items-start justify-start">
                <span class="font-semibold capitalize">detalle:</span>
                <span>{{ $item['details'] }}</span>
              </div>
              {{-- cantidad --}}
              <div class="flex flex-col items-end justify-start">
                <span class="font-semibold capitalize">cantidad:</span>
                <span>{{ $item['order_quantity'] }}</span>
              </div>
              {{-- precio --}}
              <div class="flex flex-col items-end justify-start">
                <span class="font-semibold capitalize">$subtotal:</span>
                <span>${{ number_format($item['subtotal_price'], 2) }}</span>
              </div>
            </div>
          </div>
        @empty
          <div class="flex items-center justify-start">
            <p class="text-center text-gray-600">¡El carrito está vacío!</p>
          </div>
        @endforelse
      </div>
      {{-- total --}}
      <div class="flex justify-end items-center w-full p-6">
        <span class="text-2xl font-bold text-orange-800 capitalize">total:&nbsp;$&nbsp;{{ number_format($total_price, 2) }}</span>
      </div>
    </div>
    {{-- espacio para realizar el pedido --}}
    <div class="">
      {{-- titulo de seccion --}}
      <h3 class="text-lg text-neutral-700 font-semibold">Hacer pedido</h3>
      <p class="text-lg text-neutral-700">para registrar su pedido, realice el pago del mismo</p>
      {{-- * boton MP --}}
      <div id="wallet_container"></div>
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
