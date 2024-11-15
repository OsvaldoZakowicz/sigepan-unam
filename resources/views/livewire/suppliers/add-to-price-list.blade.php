<div>
  {{-- componente alta de suministros en la lista de precios del proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="agregar suministros a la lista de precios del proveedor: {{ $supplier->company_name }}">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-300" text_color="neutral-600">volver</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="w-full flex-col gap-2">

        {{-- buscar suministros --}}
        @livewire('suppliers.search-provision', ['supplier_id' => $supplier->id])

        {{-- lista de suministros elegidos --}}
        <div class="flex flex-col gap-1 w-full">
          <span class="font-semibold capitalize">lista de suministros para el proveedor</span>
          <div class="max-h-36 overflow-y-auto overflow-x-hidden">
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th>id</x-table-th>
                  <x-table-th>suministro</x-table-th>
                  <x-table-th>$&nbsp;completar precio<span class="text-red-400">*</span></x-table-th>
                  <x-table-th>acciones</x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                @forelse ($provisions as $provision_array_key => $provision_id)
                  @livewire(
                    'suppliers.input-price-form',
                    [ 'provision_id' => $provision_id, 'supplier_id' => $supplier->id, 'provision_array_key' => $provision_array_key],
                    key($provision_array_key)
                  )
                @empty
                  <tr class="border">
                    <td colspan="3">¡lista vacia!, búsque y elija suministros para comenzar.</td>
                  </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
          </div>
        </div>

      </x-slot:content>

      <x-slot:footer class="py-2">
        <!-- grupo de botones -->
        <div class="flex">
          <x-btn-button type="button" wire:click="save" >guardar suministros listados</x-btn-button>
        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
