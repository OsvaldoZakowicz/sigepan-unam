<div>
  {{-- componente de lista de TODOS los precios --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de precios de todos los proveedores">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-index') }}" class="mx-1" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">volver a proveedores</x-a-button>
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
          <select name="trademark_filter" wire:model.live="trademark_filter" wire:click="resetPagination" id="trademark_filter" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            <option value="">seleccione una marca ...</option>
            @forelse ($trademarks as $trademark)
              <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
            @empty
              <option value="">sin marcas ...</option>
            @endforelse
          </select>
          {{-- filtro de tipo --}}
          <select name="type_filter" wire:model.live="type_filter" wire:click="resetPagination" id="type_filter" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
        {{-- controlar el alto maximo de la tabla --}}
        <div class="max-h-80 overflow-y-scroll overflow-x-hidden">
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th>id*</x-table-th>
                <x-table-th>nombre</x-table-th>
                <x-table-th>marca</x-table-th>
                <x-table-th>tipo</x-table-th>
                <x-table-th>cantidad, peso o volumen</x-table-th>
                <x-table-th>$&nbsp;precio</x-table-th>
                <x-table-th>proveedor</x-table-th>
                <x-table-th>acciones</x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              {{-- por cada suministro individual --}}
              @forelse ($all_provisions as $key => $provision)
                {{-- por cada proveedor del suministro --}}
                {{-- usare como key el indice del array superior, engloba la fila generada en la tabla --}}
                @foreach ($provision->suppliers as $supplier)
                  {{-- repetir informacion del suministro, capturar informacion del proveedor --}}
                  {{-- usare como key el id de la tabla pivote, que es unico --}}
                  <tr class="border" wire:key="{{ $supplier->pivot->id }}">
                    <x-table-td>{{ $provision->id }}</x-table-td>
                    <x-table-td>{{ $provision->provision_name }}</x-table-td>
                    <x-table-td>{{ $provision->trademark->provision_trademark_name }}</x-table-td>
                    <x-table-td>{{ $provision->type->provision_type_name }}</x-table-td>
                    <x-table-td>{{ $provision->provision_quantity }}({{ $provision->measure->measure_abrv }})</x-table-td>
                    <x-table-td>$&nbsp;<span class="font-semibold text-neutral-600">{{ $supplier->pivot->price }}</span></x-table-td>
                    <x-table-td>{{ $supplier->company_name }}</x-table-td>
                    <x-table-td>
                      <div class="flex gap-1">
                        <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600" title="ver lista de precios del proveedor">precios</x-a-button>
                      </div>
                    </x-table-td>
                  </tr>
                @endforeach
              @empty
                <tr class="border">
                  <td colspan="8">sin registros!</td>
                </tr>
              @endforelse
            </x-slot:tablebody>
          </x-table-base>
        </div>
      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- nota --}}
        <p class="text-xs text-neutral-600 font-semibold">*cantidad de suministros NO repetidos, los IDs repetidos indican que un mismo suministro se vende para diferentes proveedores, con sus respectivos precios.</p>
        {{-- paginacion --}}
        {{ $all_provisions->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
