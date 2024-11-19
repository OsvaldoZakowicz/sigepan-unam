<div class="w-full">
  {{-- desplegable --}}
  <div x-data="{ open: true }">
    <x-div-toggle title="cuadro de búsqueda" class="px-1">
      {{-- formulario de busqueda --}}
      <form class="w-full">
        <div class="flex flex-col w-full">
          {{-- buscar --}}
          <div class="flex w-full gap-1 bg-neutral-100 p-1 border-t border-x border-neutral-200">
            {{-- termino de busqueda --}}
            <div class="flex flex-col w-full justify-end">
              <x-input-label>buscar suministros</x-input-label>
              <x-text-input wire:model.live="search" wire:click="resetPagination" name="search" type="text" placeholder="ingrese un id o termino de búsqueda ..." />
            </div>
            {{-- filtrar por marca --}}
            <div class="flex flex-col w-full justify-end">
              <select wire:model.live="search_tr" wire:click="resetPagination" name="search_tr" id="search_tr" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
              <select wire:model.live="search_ty" wire:click="resetPagination" name="search_ty" id="search_ty" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                <option selected value="">seleccione un tipo ...</option>
                @forelse ($provision_types as $ty)
                  <option value="{{ $ty->id }}">{{ $ty->provision_type_name }}</option>
                @empty
                  <option value="">sin opciones ...</option>
                @endforelse
              </select>
            </div>
          </div>
          {{-- seleccion de resultado --}}
          <div class="flex flex-col w-full border-t border-neutral-200">
            @forelse ($provisions as $provision)
              <div wire:key="{{ $provision->id }}" class="flex justify-between p-1 border-x border-b">
                {{-- datos de suministro --}}
                <div class="flex">
                  <span class="font-semibold">{{ $provision->type->provision_type_name }}:&nbsp;</span>
                  <span>{{ $provision->provision_name }}&nbsp;</span>
                  <span>{{ $provision->provision_quantity }}({{ $provision->measure->measure_abrv }}),&nbsp;</span>
                  <span class="font-semibold">marca:&nbsp;</span>
                  <span>{{ $provision->trademark->provision_trademark_name }}&nbsp;</span>
                  {{-- consultar precio solo al editar --}}
                  @if ($is_editing)
                    <span class="font-semibold">,&nbsp;precio:&nbsp;</span>
                    <span>${{ $provision->pivot->price }}</span>
                  @endif
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
              {{ $provisions->links() }}
            </div>
          </div>
        </div>
      </form>
    </x-div-toggle>
  </div>
</div>
