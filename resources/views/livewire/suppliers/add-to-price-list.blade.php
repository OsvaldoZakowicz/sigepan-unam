<div>
  {{-- componente alta de suministros en la lista de precios del proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section>

      <x-slot:title>
        <span>Alta de precios de suministros para el proveedor:&nbsp;</span>
        <span class="font-semibold">{{ $supplier->company_name }}</span>
      </x-slot:title>

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}"
        bg_color="neutral-100"
        border_color="neutral-300"
        text_color="neutral-600"
        >volver
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="w-full flex-col gap-2">

        {{-- buscar suministros --}}
        @livewire('suppliers.search-provision', ['supplier_id' => $supplier->id])

        {{-- lista de suministros elegidos --}}
        <div class="flex flex-col gap-1 w-full">
          <span class="font-semibold capitalize">lista de suministros elegidos para el alta</span>
          <div class="max-h-72 overflow-y-auto overflow-x-hidden">
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th class="text-end w-12">id</x-table-th>
                  <x-table-th class="text-start">nombre</x-table-th>
                  <x-table-th class="text-start">marca</x-table-th>
                  <x-table-th class="text-start">tipo</x-table-th>
                  <x-table-th class="text-end">cantidad</x-table-th>
                  <x-table-th class="text-end w-1/3">$&nbsp;precio <span class="text-red-400">*</span> </x-table-th>
                  <x-table-th class="text-start w-16">quitar</x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                {{-- prices es un array donde cada elemento sera una fila de suministro + input precio --}}
                @forelse ($prices as $key => $price_item)
                  <tr wire:key="{{ $key }}">
                    <x-table-td class="text-end">
                      {{ $price_item['provision']->id }}
                    </x-table-td>
                    <x-table-td
                      title="{{ $price_item['provision']->provision_short_description }}"
                      class="cursor-pointer text-start">
                      {{ $price_item['provision']->provision_name }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $price_item['provision']->trademark->provision_trademark_name }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $price_item['provision']->type->provision_type_name }}
                    </x-table-td>
                    <x-table-td class="text-end">
                      {{ $price_item['provision']->provision_quantity }}&nbsp;
                      ({{ $price_item['provision']->measure->measure_abrv }})
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{-- input precio --}}
                      @error('prices.'.$key.'.price')<span class="text-red-400 text-xs capitalize">{{ $message }}</span>@enderror
                      <div class="flex justify-start items-center">
                        {{-- simbolo de $ --}}
                        <span>$&nbsp;</span>
                        {{-- precio --}}
                        <x-text-input
                          id="prices_{{ $key }}_price"
                          wire:model.defer="prices.{{ $key }}.price"
                          type="text"
                          placeholder="precio ..."
                          class="w-full text-right"/>
                      </div>
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{-- boton para quitar de la lista --}}
                      <span
                        title="quitar de la lista."
                        wire:click="removeFromPriceList({{ $key }})"
                        class="font-semibold cursor-pointer leading-none p-1 bg-red-200 text-neutral-600 border-red-300 rounded-sm"
                        >&times;
                      </span>
                    </x-table-td>
                  </tr>
                @empty
                  <tr class="border">
                    <td colspan="7">¡lista vacia!</td>
                  </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
          </div>
        </div>

      </x-slot:content>

      <x-slot:footer class="py-2">

        <!-- grupo de botones -->
        <div class="flex gap-2">

          <x-a-button
            wire:navigate
            href="#"
            wire:click="setPricesList()"
            bg_color="neutral-100"
            border_color="neutral-300"
            text_color="neutral-600"
            >vaciar lista
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            >guardar precios
          </x-btn-button>

        </div>

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
