<div class="w-full">
  {{-- formulario de busqueda --}}
  <form class="w-full">

    <div class="flex flex-col w-full">

      {{-- buscar --}}
      <div class="flex w-full">
        {{-- termino de busqueda --}}
        <div class="flex flex-col w-full">
          <x-input-label>buscar suministros</x-input-label>
          <x-text-input wire:model.live="search" name="search" type="text" placeholder="ingrese un id o termino de búsqueda ..." />
        </div>
      </div>

      {{-- seleccion de resultado --}}
      <div class="flex flex-col w-full">
        {{-- <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>nombre</x-table-th>
              <x-table-th>cantidad</x-table-th>
              <x-table-th>marca</x-table-th>
              <x-table-th>tipo</x-table-th>
              <x-table-th>seleccion</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
              <tr wire:key="{{ $provision->id }}" class="border">
                <x-table-td>{{ $provision->id }}</x-table-td>
                <x-table-td>{{ $provision->provision_name }}</x-table-td>
                <x-table-td>{{ $provision->provision_quantity }}({{ $provision->measure->measure_abrv }})</x-table-td>
                <x-table-td>{{ $provision->trademark->provision_trademark_name }}</x-table-td>
                <x-table-td>{{ $provision->type->provision_type_name }}</x-table-td>
                <x-table-td>
                  <span wire:click="addProvision({{ $provision->id }})" class="font-bold cursor-pointer text-lg leading-none">&plus;</span>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="5">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>
        <div class="flex justify-end items-center gap-4 py-1">
          {{ $provisions->links() }}
        </div> --}}

        @forelse ($provisions_filter as $provision)
          <div wire:key="{{ $provision->id }}" class="flex justify-between p-1 border-x border-b">
            {{-- datos de suministro --}}
            <div class="flex">
              <span class="font-semibold">{{ $provision->type->provision_type_name }}:&nbsp;</span>
              <span>{{ $provision->provision_name }}&nbsp;</span>
              <span>{{ $provision->provision_quantity }}({{ $provision->measure->measure_abrv }}),&nbsp;</span>
              <span class="font-semibold">marca:&nbsp;{{ $provision->trademark->provision_trademark_name }}</span>
            </div>
            {{-- botones --}}
            <div class="flex">
              <span wire:click="addProvision({{ $provision->id }})" class="font-bold cursor-pointer text-lg leading-none px-1 mx-1 bg-neutral-100 text-neutral-600 border-neutral-300 rounded-sm" title="agregar a la lista.">&plus;</span>
            </div>
          </div>
        @empty
          <div class="border">
            <span colspan="5">sin registros!</span>
          </div>
        @endforelse
        <div class="flex justify-end items-center gap-4 py-1">
          {{ $provisions_filter->links() }}
        </div>
      </div>

    </div>

  </form>
</div>
