<div class="w-full">

  {{-- desplegable --}}
  <x-div-toggle x-data="{ open: false }" title="cuadro de búsqueda" class="relative p-1">

    <x-slot:subtitle>
      <span class="font-semibold">Mostrando:&nbsp;</span>
      @if ($is_editing)
        <span>Suministros o packs de la lista de precios actual del proveedor.</span>
      @else
        <span>Suministros o packs no asignados al proveedor.</span>
      @endif
      <span>Búsque y elija suministros o packs para la lista.</span>
    </x-slot:subtitle>

    {{-- seccion de acciones --}}
    <div class="absolute -top-2 right-1 flex w-1/2 justify-end items-center gap-1">

      {{-- boton para cambiar entre buscar suministros individuales o packs --}}
      <div class="inline-flex items-center gap-1 p-1 border border-neutral-200 bg-neutral-100 rounded-md cursor-pointer">
        <label for="toggle">buscar packs</label>
        <input type="checkbox" wire:click="toggleSearch" name="toggle" id="toggle" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
      </div>

    </div>

    @if ($toggle)
      {{-- mostrar busqueda depacks --}}
      {{-- packs --}}
      <div class="flex flex-col gap-1 w-full">

        {{-- busqueda --}}
        <div class="flex w-full gap-1 bg-neutral-100 p-1 border border-neutral-200">

          {{-- termino de busqueda --}}
          <div class="flex flex-col w-full justify-end">
            <x-input-label>buscar packs</x-input-label>
            <x-text-input
              wire:model.live="search_pack"
              wire:click="resetPagination"
              name="search_pack"
              type="text"
              placeholder="ingrese un id o termino de búsqueda ..." />
          </div>

          {{-- filtrar por marca --}}
          <div class="flex flex-col w-full justify-end">
            <select
              name="search_tr_pack"
              id="search_tr_pack"
              wire:model.live="search_tr_pack"
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
              name="search_ty_pack"
              id="search_ty_pack"
              wire:model.live="search_ty_pack"
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

        {{-- seleccion del resultado --}}
        <div class="flex flex-col gap-1 w-full border-neutral-200">

          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="text-end w-12">id</x-table-th>
                <x-table-th class="text-start">nombre</x-table-th>
                <x-table-th class="text-start">marca</x-table-th>
                <x-table-th class="text-start">tipo</x-table-th>
                <x-table-th class="text-end">
                  <span>cantidad</span>
                  <x-quest-icon title="indica si se trata de una unidad (1), o pack de unidades (mas de uno)"/>
                </x-table-th>
                <x-table-th class="text-end">
                  <span>volumen</span>
                  <x-quest-icon title="kilogramos (kg), litros (lts) o unidades (un)"/>
                </x-table-th>
                @if ($is_editing) <x-table-th class="text-end w-1/3">$&nbsp;precio</x-table-th> @endif
                <x-table-th class="text-start w-16">elegir</x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              @forelse ($packs as $pack)
              <tr wire:key="{{$pack->id}}" class="border">
                  <x-table-td class="text-end">
                    {{ $pack->id }}
                  </x-table-td>
                  <x-table-td
                    class="text-start">
                    {{ $pack->pack_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pack->provision->trademark->provision_trademark_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pack->provision->type->provision_type_name }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    pack x {{ $pack->pack_units }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    {{ $pack->pack_quantity }}&nbsp;({{ $pack->provision->measure->measure_abrv }})
                  </x-table-td>
                  @if ($is_editing)
                  <x-table-td class="text-end">
                    $precio
                  </x-table-td>
                  @endif
                  <x-table-td class="text-start">
                    <div class="w-full inline-flex gap-1 justify-start items-center">
                      <span
                        wire:click="addPack({{ $pack }})"
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
            {{-- paginacion de packs --}}
            {{ $packs->links() }}
          </div>

        </div>

      </div>
    @else
      {{-- mostrar busqueda de suministros --}}
      {{-- suministros --}}
      <div class="flex flex-col gap-1 w-full">

        {{-- busqueda --}}
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

        {{-- seleccion de resultado --}}
        <div class="flex flex-col gap-1 w-full border-neutral-200">

          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="text-end w-12">id</x-table-th>
                <x-table-th class="text-start">nombre</x-table-th>
                <x-table-th class="text-start">marca</x-table-th>
                <x-table-th class="text-start">tipo</x-table-th>
                <x-table-th class="text-end">
                  <span>cantidad</span>
                  <x-quest-icon title="indica si se trata de una unidad (1), o pack de unidades (mas de uno)"/>
                </x-table-th>
                <x-table-th class="text-end">
                  <span>volumen</span>
                  <x-quest-icon title="kilogramos (kg), litros (lts) o unidades (un)"/>
                </x-table-th>
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
                    <span>1</span>
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
                        wire:click="addProvision({{ $provision }})"
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
                  <td colspan="8">¡sin registros!</td>
                @else
                  <td colspan="7">¡sin registros!</td>
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
    @endif

  </x-div-toggle>
</div>
