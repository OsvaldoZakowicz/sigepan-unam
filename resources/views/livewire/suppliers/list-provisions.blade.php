<div>
  {{-- componente listar suministros --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de suministros">
      <x-a-button wire:navigate href="{{ route('suppliers-provisions-create') }}" class="mx-1">crear suministro</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar suministro:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search" name="search" wire:click="resetPagination()" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
          {{-- filtro de marca --}}
          <select name="trademark_filter" wire:model.live="trademark_filter" wire:click="resetPagination()" id="trademark_filter" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            <option value="">seleccione una marca ...</option>
            @forelse ($trademarks as $trademark)
              <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
            @empty
              <option value="">sin marcas ...</option>
            @endforelse
          </select>
          {{-- filtro de tipo --}}
          <select name="type_filter" wire:model.live="type_filter" wire:click="resetPagination()" id="type_filter" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
              <x-table-th>id</x-table-th>
              <x-table-th>nombre</x-table-th>
              <x-table-th>marca</x-table-th>
              <x-table-th>tipo</x-table-th>
              <x-table-th>cantidad, volumen o peso</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
            <tr wire:key="{{$provision->id}}" class="border">
                <x-table-td>{{ $provision->id }}</x-table-td>
                <x-table-td
                  title="{{ $provision->provision_short_description }}"
                  class="cursor-pointer" >{{ $provision->provision_name }}</x-table-td>
                <x-table-td>{{ $provision->trademark->provision_trademark_name }}</x-table-td>
                <x-table-td>{{ $provision->type->provision_type_name }}</x-table-td>
                <x-table-td>{{ $provision->provision_quantity }}&nbsp;({{ $provision->measure->measure_abrv }})</x-table-td>
                <x-table-td>
                  <div class="w-full inline-flex gap-2 justify-start items-center">

                    <x-a-button wire:navigate href="{{ route('suppliers-provisions-edit', $provision->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>

                    <x-btn-button type="button" wire:navigate wire:click="delete({{ $provision->id }})" wire:confirm="¿Desea borrar el registro?, eliminar la provision afectara a la lista de precios de los proveedores." color="red">eliminar</x-btn-button>

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
