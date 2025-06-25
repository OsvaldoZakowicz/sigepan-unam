<div>
  {{-- componente lista de precios de un proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section>
      <x-slot:title>
        <span>lista de precios del proveedor:&nbsp;</span>
        <span class="font-semibold">{{ $supplier->company_name }},&nbsp;</span>
        <span>El proveedor se encuentra:&nbsp;</span>
        @if ($supplier->status_is_active)
          <x-text-tag color="emerald">
            activo
            <x-quest-icon title="El proveedor esta activo para la panaderia, puede contactarse para presupuestos y ordenes de compras. Puede asignarle suministros y generarle una lista de precios"/>
          </x-text-tag>
        @else
          <x-text-tag color="neutral">
            inactivo
            <x-quest-icon title="El proveedor no esta activo debido a {{ $supplier->status_description }}"/>
          </x-text-tag>
        @endif
      </x-slot:title>
      <div class="flex gap-2">

        <x-a-button
          wire:navigate
          href="{{ route('suppliers-suppliers-index', $supplier->id) }}"
          bg_color="neutral-100"
          border_color="neutral-300"
          text_color="neutral-600"
          >volver a proveedores
        </x-a-button>

        @if ($supplier->deleted_at === null)
          <x-a-button
            wire:navigate
            href="{{ route('suppliers-suppliers-price-edit', $supplier->id) }}"
            bg_color="neutral-100"
            border_color="neutral-300"
            text_color="neutral-600"
            >editar precios
          </x-a-button>

          <x-a-button
            wire:navigate
            href="{{ route('suppliers-suppliers-price-create', $supplier->id) }}"
            >agregar precios
          </x-a-button>
        @endif

      </div>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>

        {{-- busqueda --}}
        <div class="flex items-end justify-start w-full gap-1">

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
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- filtro de marca --}}
          <div class="flex flex-col w-1/4">
            <label for="trademark_filter">filtrar por marca</label>
            <select
              name="trademark_filter"
              id="trademark_filter"
              wire:model.live="trademark_filter"
              wire:click="resetPagination"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

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
              id="type_filter"
              wire:model.live="type_filter"
              wire:click="resetPagination"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

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
            <div class="inline-flex items-center gap-1 p-1 border rounded-md cursor-pointer border-neutral-200 bg-neutral-100">
              <label for="toggle">precios de packs</label>
              <input type="checkbox" wire:click="toggleSearch" name="toggle" id="toggle" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>
          </div>

          {{-- limpiar filtos --}}
          <x-a-button
            href="#"
            wire:click='limpiar()'
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar filtros
          </x-a-button>

        </div>

      </x-slot:header>

      <x-slot:content class="flex-col w-full">
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-12 text-end">id</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">marca</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-end">
                <span>volumen</span>
                <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
              </x-table-th>
              <x-table-th class="text-end">$precio</x-table-th>
              <x-table-th class="w-48 text-start">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @if ($toggle)
              {{-- lista de packs --}}
              @forelse ($packs_with_price as $pwp)
                <tr class="border" wire:key="{{ $pwp->id }}">
                  <x-table-td class="text-end">
                    {{ $pwp->id }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pwp->pack_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pwp->provision->trademark->provision_trademark_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pwp->provision->type->provision_type_name }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    {{ convert_measure($pwp->pack_quantity, $pwp->provision->measure) }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    ${{ toMoneyFormat($pwp->pivot->price) }}
                  </x-table-td>
                  <x-table-td class="text-start">

                    <x-btn-button
                      type="button"
                      wire:navigate
                      wire:click="deletePack({{ $pwp->id }})"
                      color="red"
                      wire:confirm="¿Desea borrar el registro?, eliminará el precio del proveedor: {{ $supplier->company_name }}, para el pack: {{ $pwp->pack_name }} {{ $pwp->provision->trademark->provision_trademark_name }} de {{ $pwp->pack_quantity }}({{ $pwp->provision->measure->unit_symbol }})"
                      >eliminar
                    </x-btn-button>

                  </x-table-td>
                </tr>
              @empty
                <tr class="border">
                  <td colspan="7">¡sin registros!</td>
                </tr>
              @endforelse
            @else
              {{-- lista de suministros --}}
              @forelse ($provisions_with_price as $pwp)
                <tr class="border" wire:key="{{ $pwp->id }}">
                  <x-table-td class="text-end">
                    {{ $pwp->id }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pwp->provision_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pwp->trademark->provision_trademark_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $pwp->type->provision_type_name }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    {{ convert_measure($pwp->provision_quantity, $pwp->measure) }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    ${{ toMoneyFormat($pwp->pivot->price) }}
                  </x-table-td>
                  <x-table-td class="text-start">

                    <x-btn-button
                      type="button"
                      wire:navigate
                      wire:click="deleteProvision({{ $pwp->id }})"
                      color="red"
                      wire:confirm="¿Desea borrar el registro?, eliminará el precio del proveedor: {{ $supplier->company_name }}, para el suministro: {{ $pwp->provision_name }} {{ $pwp->trademark->provision_trademark_name }} de {{ $pwp->provision_quantity }}({{ $pwp->measure->unit_symbol }})"
                      >eliminar
                    </x-btn-button>

                  </x-table-td>
                </tr>
              @empty
                <tr class="border">
                  <td colspan="7">¡sin registros!</td>
                </tr>
              @endforelse
            @endif
          </x-slot:tablebody>
        </x-table-base>
      </x-slot:content>

      <x-slot:footer class="py-2">
        @if ($toggle)
          {{-- paginacion de packs --}}
          {{ $packs_with_price->links() }}
        @else
          {{-- paginacion de suministros --}}
          {{ $provisions_with_price->links() }}
        @endif
      </x-slot:footer>

    </x-content-section>

  </article>

  <script>
    // escuchar el evento reset-checkbox
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('reset-checkbox', () => {
        // obtener los checkboxes por su id
        const toggleCheckbox = document.getElementById('toggle');

        // establecer checked = false
        if(toggleCheckbox) toggleCheckbox.checked = false;
      });
    });
  </script>
</div>
