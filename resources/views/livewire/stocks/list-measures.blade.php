<div>
  {{-- componente listar unidades de medida --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de unidades de medida">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- busqueda --}}
        <div class="grow">
          <div class="flex flex-col justify-start items-start">
            <label for="search" class="text-sm capitalize">buscar unidad:</label>
            {{-- termino de busqueda --}}
            <input
              type="text"
              name="search"
              id="search"
              wire:model.live="search"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

        </div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">medida</x-table-th>
              <x-table-th class="text-end">cantidad base</x-table-th>
              <x-table-th class="text-start">conversion</x-table-th>
              <x-table-th class="text-end">factor de conversion</x-table-th>
              <x-table-th class="text-start">descripcion corta</x-table-th>
              <x-table-th class="text-end">fecha de creacion</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($measures as $measure)
            <tr wire:key="{{$measure->id}}" class="border">
                <x-table-td class="text-end">
                  {{ $measure->id }}
                </x-table-td>
                <x-table-td class="text-start capitalize">
                  {{ $measure->unit_name }}&nbsp;({{ $measure->unit_symbol }})
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $measure->base_value }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $measure->conversion_unit ?? '-' }}&nbsp;{{ ($measure->conversion_symbol) ? '(' . $measure->conversion_symbol . ')' : '' }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $measure->conversion_factor ?? '-' }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $measure->short_description }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($measure->created_at, 'd-m-Y') }}
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
        {{-- paginacion --}}
        {{ $measures->links() }}
      </x-slot:footer>

    </x-content-section>
  </article>
</div>
