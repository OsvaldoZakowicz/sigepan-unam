<div>
  {{-- componente lista de precios del proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de precios del proveedor: {{ $supplier->company_name }}">
      <div class="flex gap-2">
        <x-a-button wire:navigate href="{{ route('suppliers-suppliers-index', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-300" text_color="neutral-600">volver</x-a-button>
        <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-create', $supplier->id) }}">agregar suministros</x-a-button>
      </div>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar suministro:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search" name="search" wire:click="resetPagination" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
          {{-- filtro de marca --}}
          {{-- <select name="trademark_filter" wire:model.live="trademark_filter" wire:click="resetPagination()" id="trademark_filter" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            <option value="">seleccione una marca ...</option>
            @forelse ($trademarks as $trademark)
              <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
            @empty
              <option value="">sin marcas ...</option>
            @endforelse
          </select> --}}
          {{-- filtro de tipo --}}
          {{-- <select name="type_filter" wire:model.live="type_filter" wire:click="resetPagination()" id="type_filter" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            <option value="">seleccione un tipo ...</option>
            @forelse ($provision_types as $type)
              <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
            @empty
              <option value="">sin tipos ...</option>
            @endforelse
          </select> --}}
        </form>
      </x-slot:header>

      <x-slot:content class="w-full flex-col">
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>nombre</x-table-th>
              <x-table-th>marca</x-table-th>
              <x-table-th>tipo</x-table-th>
              <x-table-th>cantidad, peso o volumen</x-table-th>
              <x-table-th>$&nbsp;precio</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
              <tr class="border" wire:key="{{ $provision->id }}">
                <x-table-td>{{ $provision->id }}</x-table-td>
                <x-table-td>{{ $provision->provision_name }}</x-table-td>
                <x-table-td>{{ $provision->trademark->provision_trademark_name }}</x-table-td>
                <x-table-td>{{ $provision->type->provision_type_name }}</x-table-td>
                <x-table-td>{{ $provision->provision_quantity }}({{ $provision->measure->measure_abrv }})</x-table-td>
                <x-table-td>$&nbsp;{{ $provision->pivot->price }}</x-table-td>
                <x-table-td>
                  acciones
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="3">¡lista vacia!, búsque y agregue suministros a esta lista para comenzar.</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>
      </x-slot:content>

      <x-slot:footer class="hidden py-2">
        {{-- paginacion --}}
        {{ $provisions->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
