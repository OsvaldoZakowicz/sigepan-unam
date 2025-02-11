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
              <x-table-th class="text-start">recetas</x-table-th>
              <x-table-th class="text-end">$&nbsp;precio</x-table-th>
              <x-table-th class="text-end">fecha de creacion</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($products as $product)
              <tr wire:key="{{ $product->id }}" class="border">
                <x-table-td class="text-end">{{ $product->id }}</x-table-td>
                <x-table-td class="text-start">{{ $product->product_name }}</x-table-td>
                <x-table-td class="text-end">-</x-table-td>
                <x-table-td class="text-end">{{ $product->product_price }}</x-table-td>
                <x-table-td class="text-end">{{ formatDateTime($product->created_at, 'd-m-Y') }}</x-table-td>
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
                <td colspan="7">¡sin registros!</td>
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
