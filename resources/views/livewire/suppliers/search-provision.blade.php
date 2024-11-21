<div class="w-full">

  {{-- desplegable --}}
  <x-div-toggle x-data="{ open: false }" title="cuadro de búsqueda" subtitle="Busque y elija suministros para la lista." class="p-1">

    <div class="flex flex-col gap-1 w-full">

      {{-- formulario de busqueda --}}
      <form class="w-full">
        <div class="flex w-full gap-1 bg-neutral-100 p-1 border border-neutral-200">

          {{-- termino de busqueda --}}
          <div class="flex flex-col w-full justify-end">
            <x-input-label>buscar suministros</x-input-label>
            <x-text-input
              wire:model.live="search"
              wire:click="resetPagination"
              name="search"
              type="text"
              placeholder="ingrese un id o termino de búsqueda ..." />
          </div>

          {{-- filtrar por marca --}}
          <div class="flex flex-col w-full justify-end">
            <select
              name="search_tr"
              id="search_tr"
              wire:model.live="search_tr"
              wire:click="resetPagination"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

              <option selected value="">seleccione una marca ...</option>

              @forelse ($trademarks as $tr)
                <option value="{{ $tr->id }}">{{ $tr->provision_trademark_name }}</option>
              @empty
                <option value="">sin opciones ...</option>
              @endforelse

            </select>
          </div>

          {{-- filtrar por tipo --}}
          <div class="flex flex-col w-full justify-end">
            <select
              name="search_ty"
              id="search_ty"
              wire:model.live="search_ty"
              wire:click="resetPagination"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

              <option selected value="">seleccione un tipo ...</option>

              @forelse ($provision_types as $ty)
                <option value="{{ $ty->id }}">{{ $ty->provision_type_name }}</option>
              @empty
                <option value="">sin opciones ...</option>
              @endforelse

            </select>
          </div>

          {{-- elegir tamaño de paginacion --}}
          <div class="flex flex-col w-full justify-end">
            <select
              name="paginas"
              id="paginas"
              wire:model.live="paginas"
              wire:click="resetPagination"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

              <option value="5"> mostrar grupos de 5 resultados</option>
              <option value="10">mostrar grupos de 10 resultados</option>
              <option value="15">mostrar grupos de 15 resultados</option>

            </select>
          </div>

        </div>
      </form>

      {{-- seleccion de resultado --}}
      <div class="flex flex-col gap-1 w-full border-neutral-200">

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">marca</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-end">cantidad</x-table-th>
              @if ($is_editing) <x-table-th class="text-end w-1/3">$&nbsp;precio</x-table-th> @endif
              <x-table-th class="text-start w-16">elegir</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
            <tr wire:key="{{$provision->id}}" class="border">
                <x-table-td class="text-end">
                  {{ $provision->id }}
                </x-table-td>
                <x-table-td
                  title="{{ $provision->provision_short_description }}"
                  class="cursor-pointer text-start">
                  {{ $provision->provision_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision->trademark->provision_trademark_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision->type->provision_type_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $provision->provision_quantity }}&nbsp;({{ $provision->measure->measure_abrv }})
                </x-table-td>
                @if ($is_editing)
                <x-table-td class="text-end">
                  $&nbsp;{{ $provision->pivot->price }}
                </x-table-td>
                @endif
                <x-table-td class="text-start">
                  <div class="w-full inline-flex gap-1 justify-start items-center">
                    <span
                      wire:click="addProvision({{ $provision->id }})"
                      title="elegir y agregar a la lista"
                      class="font-bold leading-none text-center p-1 cursor-pointer bg-neutral-100 border border-neutral-200 rounded-sm"
                      >&plus;
                    </span>
                  </div>
                </x-table-td>
            </tr>
            @empty
            <tr class="border">
              @if ($is_editing)
                <td colspan="7">¡sin registros!</td>
              @else
                <td colspan="6">¡sin registros!</td>
              @endif
            </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        <div class="w-full flex justify-end items-center gap 1">
          {{ $provisions->links() }}
        </div>

      </div>

    </div>

  </x-div-toggle>
</div>
