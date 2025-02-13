<div>
  {{-- componente listar productos --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de recetas">
      <x-a-button
        wire:navigate
        href="{{ route('stocks-products-create') }}"
        class="mx-1"
        >crear producto
      </x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden">
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">producto</x-table-th>
              <x-table-th class="text-start">descripcion</x-table-th>
              <x-table-th class="text-start">recetas</x-table-th>
              <x-table-th class="text-end">$&nbsp;precio</x-table-th>
              <x-table-th class="text-start">
                <span>publicado</span>
                <x-quest-icon title="indica si el producto aparece en la tienda para la venta"/>
              </x-table-th>
              <x-table-th class="text-end">fecha de creacion</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($products as $product)
              <tr wire:key="{{ $product->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $product->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $product->product_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ limitText($product->product_short_description) }}
                  <span class="font-semibold text-blue-300 cursor-pointer">leer mas</span>
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($product->recipes->count() > 0 )
                    <span>con {{ $product->recipes->count() }} recetas</span>
                    <span class="font-semibold text-blue-300 cursor-pointer">ver</span>
                  @else
                    <span>sin recetas</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  <span>$&nbsp;</span>
                  {{ $product->product_price }}
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($product->product_in_store)
                    <span>si</span>
                  @else
                    <span>no</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($product->created_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="flex justify-start gap-1">

                    <x-a-button
                      wire:navigate
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >editar
                    </x-a-button>

                    <x-btn-button
                      btn_type="button"
                      color="red"
                      >eliminar
                    </x-btn-button>

                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="8">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $products->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
