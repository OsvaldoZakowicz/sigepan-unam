<div>
  {{-- componente de mis pedidos --}}
  <div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-6xl mx-auto p-6 h-4/5">

    <div class="w-full">
      {{-- cabecera --}}
      <div class="flex justify-between items-center mb-4">
        {{-- titulo de seccion --}}
        <h2 class="text-xl text-neutral-700 font-semibold">Mis pedidos</h2>
      </div>

      {{-- vista de pedidos --}}
      <div class="space-y-4 max-h-72 p-3 overflow-y-auto overflow-x-hidden">
        {{-- @forelse($cart as $item)
          <div wire:key="{{ $loop->index }}" class="flex flex-col items-center justify-between border-b pb-2">
            <div class="w-full flex justify-between items-center">
              <div class="flex items-center gap-2 w-2/3">
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
              <div class="flex items-center justify-end w-1/6">
                <p class="font-semibold">cantidad:&nbsp;{{ $item['quantity'] }}</p>
              </div>
              <div class="flex items-center justify-end w-1/6">
                <p class="font-semibold">${{ number_format($item['subtotal'], 2) }}</p>
              </div>
            </div>
          </div>
        @empty
          <div class="flex items-center justify-start">
            <p class="text-center text-gray-600">¡El carrito está vacío!</p>
          </div>
        @endforelse --}}
      </div>
    </div>

  </div>
</div>
