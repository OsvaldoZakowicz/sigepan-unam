<div wire:poll>

   {{-- componente listar periodos de peticion de preordenes--}}
   <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de periodos de petición de pre ordenes">

      {{-- todo: crear periodo sin necesidad de presupuestos previos? --}}
      {{-- todo: crear a partir de un presupuesto anterior? --}}
      {{-- <x-a-button
        wire:navigate
        href="{{route('suppliers-budgets-periods-create')}}"
        class="mx-1"
        >crear nuevo período
      </x-a-button> --}}

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="search">buscar periodo</label>
            <input
              type="text"
              name="search"
              id="search"
              wire:model.live="search"
              wire:click="resetPagination()"
              placeholder="ingrese un id o codigo de período ..."
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- fecha de inicio --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_start_at">fecha de inicio desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- fecha de fin --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_end_at">fecha de fin hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
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
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">codigo de periodo</x-table-th>
              <x-table-th class="text-end">fecha de inicio</x-table-th>
              <x-table-th class="text-end">fecha de cierre</x-table-th>
              <x-table-th class="text-start">estado</x-table-th>
              <x-table-th class="text-end">fecha de creación</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($preorder_periods as $period)
              <tr wire:key="{{ $period->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $period->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $period->period_code }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($period->period_start_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($period->period_end_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- manejar tres estados distintos --}}
                  @switch($period->status->status_code)
                    @case(0)
                      {{-- programado --}}
                      <x-text-tag
                        color="neutral"
                        class="cursor-pointer"
                        >{{ $period->status->status_name }}
                        <x-quest-icon title="{{ $period->status->status_short_description }}"/>
                      </x-text-tag>
                      @break
                    @case(1)
                      {{-- abierto --}}
                      <x-text-tag
                        color="emerald"
                        class="cursor-pointer"
                        >{{ $period->status->status_name }}
                        <x-quest-icon title="{{ $period->status->status_short_description }}"/>
                      </x-text-tag>
                      @break
                    @default
                      {{-- cerrado --}}
                      <x-text-tag
                        color="red"
                        class="cursor-pointer"
                        >{{ $period->status->status_name }}
                        <x-quest-icon title="{{ $period->status->status_short_description }}"/>
                      </x-text-tag>
                  @endswitch
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($period->created_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">
                    <x-a-button
                      wire:navigate
                      href="{{ route('suppliers-preorders-show', $period->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver
                    </x-a-button>
                  </div>
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

        {{-- paginacion --}}
        {{ $preorder_periods->links() }}

        <!-- grupo de botones -->
        <div class="flex"></div>

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
