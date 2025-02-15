<div>
  <main role="store">
    <section role="products-list" class="w-full p-2 flex justify-start items-start gap-6 flex-wrap">
      @forelse ($products as $product)
        <article wire:key="{{ $product->id }}" class="bg-white rounded-lg shadow-md w-64 h-96 p-4 flex flex-col">
          <img src="{{ Storage::url($product->product_image_path) }}" alt="{{ $product->product_name }}" class="w-full h-48 object-cover rounded-t-lg">
          <div class="flex flex-col gap-2 mt-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $product->product_name }}</h2>
            <h3 class="text-sm text-gray-600">{{ $product->product_short_description }}</h3>
            <h3 class="text-2xl font-bold text-blue-600">${{ number_format($product->product_price, 2) }}</h3>
            <button
              wire:click="addToCart({{ $product }})"
              class="mt-auto bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
              >agregar al carrito
            </button>
          </div>
        </article>
      @empty
        <span>¡Sin productos a la venta!</span>
      @endforelse
    </section>
  </main>
</div>
