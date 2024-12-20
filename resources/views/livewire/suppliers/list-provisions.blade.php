<div>
  {{-- componente listar suministros --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de suministros">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-provisions-create') }}"
        class="mx-1"
        >crear suministro
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar suministro:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">

          {{-- termino de busqueda --}}
          <input
            type="text"
            name="search"
            wire:model.live="search"
            wire:click="resetPagination()"
            placeholder="ingrese un id, o termino de busqueda"
            class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />

          {{-- filtro de marca --}}
          <select
            name="trademark_filter"
            id="trademark_filter"
            wire:model.live="trademark_filter"
            wire:click="resetPagination()"
            class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

            <option value="">seleccione una marca ...</option>

            @forelse ($trademarks as $trademark)
              <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
            @empty
              <option value="">sin marcas ...</option>
            @endforelse

          </select>

          {{-- filtro de tipo --}}
          <select
            name="type_filter"
            id="type_filter"
            wire:model.live="type_filter"
            wire:click="resetPagination()"
            class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

            <option value="">seleccione un tipo ...</option>

            @forelse ($provision_types as $type)
              <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
            @empty
              <option value="">sin tipos ...</option>
            @endforelse

          </select>

        </form>

        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">marca</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-end">cantidad</x-table-th>
              <x-table-th class="text-start w-24">acciones</x-table-th>
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
                <x-table-td class="text-start">
                  <div class="w-full inline-flex gap-1 justify-start items-center">

                    <x-a-button
                      wire:click="edit({{ $provision->id }})"
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >editar</x-a-button>

                    <x-btn-button
                      type="button"
                      wire:click="delete({{ $provision->id }})"
                      wire:confirm="¿Desea borrar el registro?, eliminar un suministro hará que no este disponible para asignar a ningún proveedor."
                      color="red"
                      >eliminar</x-btn-button>

                  </div>
                </x-table-td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="7">sin registros!</td>
            </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $provisions->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>
  </article>
</div>
