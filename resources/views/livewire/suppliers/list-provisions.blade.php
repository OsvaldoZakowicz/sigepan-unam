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

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar suministro</label>
            <input
              type="text"
              name="search"
              wire:model.live="search"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- filtro de marca --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="trademark_filter">filtrar por marca</label>
            <select
              name="trademark_filter"
              id="trademark_filter"
              wire:model.live="trademark_filter"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

              <option value="">seleccione una marca ...</option>

              @forelse ($trademarks as $trademark)
                <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
              @empty
                <option value="">sin marcas ...</option>
              @endforelse

            </select>
          </div>

          {{-- filtro de tipo --}}
          <div class="flex flex-col justify-end w-1/4">
            {{-- filtro de tipo --}}
            <label for="type_filter">filtrar por tipo</label>
            <select
              name="type_filter"
              id="type_filter"
              wire:model.live="type_filter"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

              <option value="">seleccione un tipo ...</option>

              @forelse ($provision_types as $type)
                <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
              @empty
                <option value="">sin tipos ...</option>
              @endforelse

            </select>
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
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">marca</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-end">
                <span>volumen unitario</span>
                <x-quest-icon title="kilogramos (kg), litros (lts) o unidades (un)"/>
              </x-table-th>
              <x-table-th class="text-start">
                <span>packs disponibles</span>
                <x-quest-icon title="packs en los que se puede encontrar el suministro" />
              </x-table-th>
              <x-table-th class="text-start w-24">estado</x-table-th>
              <x-table-th class="text-start w-24">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
              <tr wire:key="{{$provision->id}}" class="border @if ($provision->deleted_at === 'borrado') text-neutral-400 @endif">
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
                  @php
                    $packs = $provision->packs;
                  @endphp
                  @if (count($packs) > 0)
                    @foreach ($packs as $pack)
                    <div
                      class="inline-flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 px-1 rounded-lg cursor-pointer"
                      title="volumen total:&nbsp;{{ $pack->pack_quantity }}{{ $provision->measure->measure_abrv }}">
                      <span>&times;{{ $pack->pack_units }}</span>
                    </div>
                    @endforeach
                  @else
                    <span>ninguno</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-start text-neutral-700">
                  @if ($provision->deleted_at === 'borrado')
                    <span class="font-semibold text-neutral-600" >{{ $provision->deleted_at }}</span>
                  @else
                    <span class="font-semibold text-emerald-600">{{ $provision->deleted_at }}</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="w-full inline-flex gap-1 justify-start items-center">
                    @if ($provision->deleted_at === 'activo')

                      <x-a-button
                        wire:click="edit({{ $provision->id }})"
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >editar
                      </x-a-button>

                      <x-btn-button
                        type="button"
                        wire:click="delete({{ $provision->id }})"
                        wire:confirm="¿Desea borrar el registro?, eliminar un suministro hará que no este disponible para asignar a ningún proveedor o crear packs."
                        color="red"
                        >eliminar
                      </x-btn-button>

                    @else

                      <x-a-button
                        wire:click="restore({{ $provision->id }})"
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        title="revertir el borrado de este suministro."
                        wire:confirm="¿Desea restaurar el registro?, el mismo volverá a estar disponible para proveedores y packs."
                        >restaurar
                      </x-a-button>

                    @endif
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
