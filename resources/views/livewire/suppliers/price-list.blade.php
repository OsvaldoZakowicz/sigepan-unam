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
          <span
            class="font-semibold text-emerald-600"
            >activo
            <x-quest-icon title="El proveedor esta activo para la panaderia, puede contactarse para presupuestos y ordenes de compras. Puede asignarle suministros y generarle una lista de precios"/>
          </span>
        @else
          <span
            class="font-semibold text-neutral-600"
            >inactivo
            <x-quest-icon title="El proveedor no esta activo debido a {{ $supplier->status_description }}"/>
          </span>
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

      </div>
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
            wire:click="resetPagination"
            placeholder="ingrese un id, o termino de busqueda"
            class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />

          {{-- filtro de marca --}}
          <select
            name="trademark_filter"
            id="trademark_filter"
            wire:model.live="trademark_filter"
            wire:click="resetPagination"
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
            wire:click="resetPagination"
            class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

            <option value="">seleccione un tipo ...</option>

            @forelse ($provision_types as $type)
              <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
            @empty
              <option value="">sin tipos ...</option>
            @endforelse

          </select>

        </form>
      </x-slot:header>

      <x-slot:content class="w-full flex-col">
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">marca</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-end">
                <span>volumen</span>
                <x-quest-icon title="kilogramos (kg), litros (lts) o unidades (un)"/>
              </x-table-th>
              <x-table-th class="text-end">$&nbsp;precio</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
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
                  {{ $pwp->provision_quantity }}({{ $pwp->measure->measure_abrv }})
                </x-table-td>
                <x-table-td class="text-end">
                  $&nbsp;{{ $pwp->pivot->price }}
                </x-table-td>
                <x-table-td class="text-start">

                  <x-btn-button
                    type="button"
                    wire:navigate
                    wire:click="delete({{ $pwp->id }})"
                    color="red"
                    wire:confirm="¿Desea borrar el registro?, eliminará el precio del proveedor: {{ $supplier->company_name }}, para el suministro: {{ $pwp->provision_name }} {{ $pwp->trademark->provision_trademark_name }} de {{ $pwp->provision_quantity }}({{ $pwp->measure->measure_abrv }})"
                    >eliminar
                  </x-btn-button>

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
        {{ $provisions_with_price->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
