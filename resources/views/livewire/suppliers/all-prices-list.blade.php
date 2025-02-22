<div>
  {{-- componente de lista de TODOS los precios --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de precios de todos los proveedores">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-suppliers-index') }}"
        class="mx-1"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver a proveedores
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>

        {{-- busqueda --}}
        <div class="w-full flex justify-start items-center gap-1">

          {{-- termino de busqueda --}}
          <div class="flex flex-col w-1/4">
            <label for="search">
              @if ($toggle)
                <span>buscar pack</span>
              @else
                <span>buscar suministro</span>
              @endif
            </label>
            <input
              type="text"
              name="search"
              id="search"
              wire:model.live="search"
              wire:click="resetPagination"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- filtro de marca --}}
          <div class="flex flex-col w-1/4">
            <label for="trademark_filter">filtrar por marca</label>
            <select
              name="trademark_filter"
              wire:model.live="trademark_filter"
              wire:click="resetPagination"
              id="trademark_filter"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />

              <option value="">seleccione una marca ...</option>

              @forelse ($trademarks as $trademark)
                <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
              @empty
                <option value="">sin marcas ...</option>
              @endforelse

            </select>
          </div>

          {{-- filtro de tipo --}}
          <div class="flex flex-col w-1/4">
            <label for="type_filter">filtrar por tipo</label>
            <select
              name="type_filter"
              wire:model.live="type_filter"
              wire:click="resetPagination"
              id="type_filter"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />

              <option value="">seleccione un tipo ...</option>

              @forelse ($provision_types as $type)
                <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
              @empty
                <option value="">sin tipos ...</option>
              @endforelse

            </select>
          </div>

          {{-- cambiar a lista de precios de packs --}}
          <div class="self-end justify-self-end">
            <div class="inline-flex items-center gap-1 p-1 border border-neutral-200 bg-neutral-100 rounded-md cursor-pointer">
              <label for="toggle">precios de packs</label>
              <input type="checkbox" wire:click="toggleSearch" name="toggle" id="toggle" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>
          </div>

        </div>

      </x-slot:header>

      <x-slot:content class="w-full flex-col">
        {{-- controlar el alto maximo de la tabla --}}
        <div class="max-h-80 overflow-y-scroll overflow-x-hidden">
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="text-end w-12">
                  <span>id</span>
                  <x-quest-icon title="los IDs repetidos indican que un mismo suministro se vende para diferentes proveedores, con sus respectivos precios."/>
                </x-table-th>
                <x-table-th class="text-start">nombre</x-table-th>
                <x-table-th class="text-start">marca</x-table-th>
                <x-table-th class="text-start">tipo</x-table-th>
                <x-table-th class="text-start">proveedor</x-table-th>
                <x-table-th class="text-end">
                  <span>cantidad</span>
                  <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
                </x-table-th>
                <x-table-th class="text-end">$&nbsp;precio</x-table-th>
                <x-table-th class="text-start w-48">acciones</x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              @if ($toggle)
                {{-- lista de packs --}}
                {{-- por cada pack individual --}}
                @forelse ($all_packs as $key => $pack)
                  {{-- por cada proveedor del pack --}}
                  {{-- usare como key el indice del array superior, engloba la fila generada en la tabla --}}
                  @foreach ($pack->suppliers as $supplier)
                    {{-- repetir informacion del pack --}}
                    {{-- usare como key el id de la tabla pivote, que es unico --}}
                    <tr class="border" wire:key="{{ $supplier->pivot->id }}">
                      <x-table-td class="text-end">
                        {{ $pack->id }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $pack->pack_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $pack->provision->trademark->provision_trademark_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $pack->provision->type->provision_type_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $supplier->company_name }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ convert_measure($pack->pack_quantity, $pack->provision->measure) }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        $&nbsp;{{ $supplier->pivot->price }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        <div class="flex gap-1">

                          <x-a-button
                            wire:navigate
                            href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}"
                            bg_color="neutral-100"
                            border_color="neutral-200"
                            text_color="neutral-600"
                            title="ver lista de precios del proveedor"
                            >ver proveedor
                          </x-a-button>

                        </div>
                      </x-table-td>
                    </tr>
                  @endforeach
                @empty
                  <tr class="border">
                    <td colspan="8">¡sin registros!</td>
                  </tr>
                @endforelse
              @else
                {{-- lista de suministros --}}
                {{-- por cada suministro individual --}}
                @forelse ($all_provisions as $key => $provision)
                  {{-- por cada proveedor del suministro --}}
                  {{-- usare como key el indice del array superior, engloba la fila generada en la tabla --}}
                  @foreach ($provision->suppliers as $supplier)
                    {{-- repetir informacion del suministro, capturar informacion del proveedor --}}
                    {{-- usare como key el id de la tabla pivote, que es unico --}}
                    <tr class="border" wire:key="{{ $supplier->pivot->id }}">
                      <x-table-td class="text-end">
                        {{ $provision->id }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $provision->provision_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $provision->trademark->provision_trademark_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $provision->type->provision_type_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $supplier->company_name }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ convert_measure($provision->provision_quantity, $provision->measure) }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        $&nbsp;{{ $supplier->pivot->price }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        <div class="flex gap-1">

                          <x-a-button
                            wire:navigate
                            href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}"
                            bg_color="neutral-100"
                            border_color="neutral-200"
                            text_color="neutral-600"
                            title="ver lista de precios del proveedor"
                            >ver proveedor
                          </x-a-button>

                        </div>
                      </x-table-td>
                    </tr>
                  @endforeach
                @empty
                  <tr class="border">
                    <td colspan="8">¡sin registros!</td>
                  </tr>
                @endforelse
              @endif
            </x-slot:tablebody>
          </x-table-base>
        </div>
      </x-slot:content>

      <x-slot:footer class="py-2">
        @if ($toggle)
          @if($all_packs->hasPages())
            {{-- nota --}}
            <p class="text-xs text-neutral-600 font-semibold">Indica cantidad de packs NO repetidos:</p>
          @endif
          {{-- paginacion de packs --}}
          {{ $all_packs->links() }}
        @else
          @if ($all_provisions->hasPages())
            {{-- nota --}}
            <p class="text-xs text-neutral-600 font-semibold">Indica cantidad de suministros NO repetidos:</p>
          @endif
          {{-- paginacion de suministros --}}
          {{ $all_provisions->links() }}
        @endif

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
