<div>
  {{-- componente listar compras --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de compras">
      <x-a-button
        wire:navigate
        href="{{ route('purchases-purchases-create') }}"
        class="mx-1"
        >registrar compra
      </x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar compra</label>
            <input
              type="text"
              name="search_purchase"
              wire:model.live="search_purchase"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

        </div>

        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>

      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">
                id
              </x-table-th>
              <x-table-th class="text-start">
                proveedor
              </x-table-th>
              <x-table-th class="text-start">
                estado
              </x-table-th>
              <x-table-th class="text-end">
                $&nbsp;costo
              </x-table-th>
              <x-table-th class="text-start">
                fecha de compra
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($purchases as $purchase)
              <tr wire:key="{{ $purchase->id }}">
                <x-table-td class="text-end">
                  {{ $purchase->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $purchase->supplier->company_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $purchase->status }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $purchase->total_price }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $purchase->purchase_date }}
                </x-table-td>
                <x-table-td class="text-start">
                  acciones
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="6">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $purchases->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
