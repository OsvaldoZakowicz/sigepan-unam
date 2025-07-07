<div class="w-full">

  {{-- desplegable --}}
  <x-div-toggle x-data="{ open: false }" title="cuadro de búsqueda" class="relative p-1">

    <x-slot:subtitle>
      <span>Búsque y elija suministros para la receta.</span>
    </x-slot:subtitle>

    {{-- seccion de acciones --}}
    <div class="absolute flex items-center justify-end w-1/2 gap-1 -top-2 right-1"></div>

    {{-- suministros --}}
    <div class="flex flex-col w-full gap-1">

      {{-- busqueda --}}
      <div class="flex items-end justify-start w-full gap-1 p-1 border bg-neutral-100 border-neutral-200">

        {{-- termino de busqueda --}}
        <div class="flex flex-col justify-end w-56">
          <x-input-label>buscar suministros</x-input-label>
          <x-text-input
            wire:model.live="search"
            wire:click="resetPagination"
            name="search"
            type="text"
            placeholder="ingrese un id o termino de búsqueda ..." />
        </div>

        {{-- filtrar por tipo --}}
        <div class="flex flex-col justify-end w-56">
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
        <div class="flex flex-col justify-end w-56">
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

        <x-a-button
          href="#"
          wire:click="clearFilters()"
          bg_color="neutral-200"
          border_color="neutral-200"
          text_color="neutral-600"
          >limpiar filtros
        </x-a-button>

      </div>

      {{-- seleccion de resultado --}}
      <div class="flex flex-col w-full gap-1 border-neutral-200">
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-12 text-end">id</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-end">
                <span>unidad de medida</span>
                <x-quest-icon title="indica la unidad de medida del suministro"/>
              </x-table-th>
              <x-table-th class="w-16 text-start">elegir</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions_categories as $pc)
              <tr wire:key="{{$pc->id}}" class="border">
                <x-table-td class="text-end">
                  {{ $pc->id }}
                </x-table-td>
                <x-table-td
                  class="cursor-pointer text-start">
                  {{ $pc->provision_category_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $pc->provision_type->provision_type_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  <span>{{ $pc->measure->unit_name }}&nbsp;<span class="capitalize">({{ $pc->measure->unit_symbol }})</span></span>
                  @if ($pc->measure->conversion_unit !== null)
                    <span>{{ $pc->measure->conversion_factor }}&nbsp;{{ $pc->measure->conversion_symbol }}</span>
                  @endif
                </x-table-td>
                {{-- acciones --}}
                <x-table-td class="text-start">
                  <div class="inline-flex items-center justify-start w-full gap-1">
                    <span
                      wire:click="addProvisionCategory({{ $pc->id }})"
                      title="elegir y agregar a la lista"
                      class="p-1 font-bold leading-none text-center border rounded-sm cursor-pointer bg-neutral-100 border-neutral-200"
                      >&plus;
                    </span>
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

        <div class="flex items-center justify-end w-full gap 1">
          {{ $provisions_categories->links() }}
        </div>

      </div>

    </div>

  </x-div-toggle>

</div>
