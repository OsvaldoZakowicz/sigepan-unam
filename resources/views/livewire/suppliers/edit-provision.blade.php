<div>
  {{-- componente editar suministro --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="editar suministro"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <span class="mb-1 font-bold">formulario</span>

        @if (!$can_edit)
          <span class="mb-1">El suministro esta asociado a proveedores, solo puede editar la descripcion o los packs</span>
        @endif

        <form wire:submit="save" wire:confirm="¿Editar este suministro?, confirme que desea hacer cambios en el suministro individual y/o packs del mismo" class="w-full flex flex-col gap-2">

          <x-fieldset-base tema="suministro individual" class="w-full">

            {{-- nombre --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="provision_name"
                  class="font-normal"
                  >nombre del suministro
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_name')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <input
                wire:model="provision_name"
                name="provision_name"
                type="text"
                @readonly(!$can_edit)
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 @if (!$can_edit)bg-neutral-100 @endif"
              />
            </div>

            {{-- marca --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="provision_trademark_id"
                  class="font-normal"
                  >marca
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_trademark_id')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <select
                name="provision_trademark_id"
                wire:model="provision_trademark_id"
                id="provision_trademark_id"
                @if (!$can_edit) disabled @endif
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 @if (!$can_edit) bg-neutral-100 @endif">

                <option value="" selected>seleccione una marca ...</option>

                @foreach ($trademarks as $trademark)
                  <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
                @endforeach

              </select>
            </div>

            {{-- tipo --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="provision_type_id"
                  class="font-normal"
                  >tipo
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_type_id')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <select
                name="provision_type_id"
                wire:model="provision_type_id"
                id="provision_type_id"
                @if (!$can_edit) disabled @endif
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 @if (!$can_edit) bg-neutral-100 @endif">

                <option value="" selected>seleccione un tipo ...</option>

                @foreach ($provision_types as $type)
                  <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
                @endforeach

              </select>
            </div>

            {{-- unidad de medida --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="measure_id"
                  class="font-normal"
                  >unidad de medida
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('measure_id')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <select
                name="measure_id"
                wire:model="measure_id"
                id="measure_id"
                @if (!$can_edit) disabled @endif
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 @if (!$can_edit) bg-neutral-100 @endif">

                <option value="" selected>seleccione una unidad ...</option>

                @foreach ($measures as $measure)
                  <option
                    value="{{ $measure->id }}"
                    title="{{ $measure->measure_short_description }}" class="cursor-pointer">
                    {{ $measure->measure_name }}({{ $measure->measure_abrv }})
                  </option>
                @endforeach

              </select>
            </div>

            {{-- cantidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="provision_quantity"
                  class="font-normal"
                  >volumen
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_quantity')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <input
                wire:model="provision_quantity"
                name="provision_quantity"
                type="text"
                @readonly(!$can_edit)
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 @if (!$can_edit)bg-neutral-100 @endif"
              />
            </div>

            {{-- descripcion --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:grow">
              <span>
                <x-input-label
                  for="provision_short_description"
                  class="font-normal"
                  >descripcion
                </x-input-label>
              </span>
              @error('provision_short_description')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <x-text-input wire:model="provision_short_description" name="provision_short_description" />
            </div>

          </x-fieldset-base>

          <x-fieldset-base tema="packs del suministro" class="w-full">

            {{-- pack --}}
            <div class="flex justify-start items-end p-2 w-full">

              {{-- indicar cantidad del pack --}}
              <div class="flex flex-col gap-1 pr-2 md:w-1/2 lg:w-1/4">
                <label for="pack_units">crear o editar la lista de packs del suministro</label>
                <div class="flex items-center justify-start gap-1">
                  <select
                    id="pack_units"
                    name="pack_units"
                    wire:model="pack_units"
                    class="grow p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                    <option value="">indique un pack a crear ...</option>
                    <option value="6">pack de 6</option>
                    <option value="8">pack de 8</option>
                    <option value="10">pack de 10</option>
                    <option value="12">pack de 12</option>
                  </select>
                  <x-a-button
                    href="#"
                    wire:click="createPack()"
                    bg_color="neutral-100"
                    border_color="neutral-100"
                    text_color="neutral-600"
                    >seleccionar
                  </x-a-button>
                </div>
              </div>

              {{-- mostrar packs a crear o editar --}}
              <div class="flex flex-col md:w-1/2 lg:grow">
                <span class="ml-2">lista de packs, use el icono <strong>x</strong> para eliminar o cancelar un pack</span>
                <div class="flex justify-start items-center gap-1 ml-2 p-1 h-8 border border-dashed border-neutral-200 leading-none">

                  {{-- packs creados --}}
                  @foreach ($packs as $key => $pack)
                    <div class="flex items-center justify-start gap-1 border border-emerald-600 bg-emerald-400 py-px px-1 rounded-lg">
                      <span class="text-xs uppercase font-semibold">activo</span>
                      <span class="text-sm text-neutral-600 lowercase">pack de {{ $pack->pack_units }}</span>
                      <span wire:click="deletePack({{ $pack->id }}, {{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="eliminar este pack">&times;</span>
                    </div>
                  @endforeach

                  {{-- packs borrados con soft delete --}}
                  @foreach ($soft_deleted_packs as $key => $sd_pack)
                    <div class="flex items-center justify-start gap-1 border border-neutral-300 bg-neutral-50 py-px px-1 rounded-lg text-neutral-500">
                      <span class="text-xs uppercase font-semibold">borrado</span>
                      <span class="text-sm text-neutral-600 lowercase">pack de {{ $sd_pack->pack_units }}</span>
                      <span wire:click="restoreSoftDeleted({{ $sd_pack->id }}, {{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="restaurar este pack">&times;</span>
                    </div>
                  @endforeach

                  {{-- packs a crear --}}
                  @foreach ($new_packs as $key => $pack )
                    <div class="flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 py-px px-1 rounded-lg">
                      <span class="text-xs uppercase font-semibold">crear</span>
                      <span class="text-sm text-neutral-600 lowercase">pack de {{ $pack }}</span>
                      <span wire:click="cancelPackCreation({{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="cancelar creación">&times;</span>
                    </div>
                  @endforeach

                  {{-- packs a restaurar del soft delete --}}
                  @foreach ($packs_to_restore as $key => $pack)
                    <div class="flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 py-px px-1 rounded-lg">
                      <span class="text-xs uppercase font-semibold">restaurar</span>
                      <span class="text-sm text-neutral-600 lowercase">pack de {{ $pack->pack_units }}</span>
                      <span wire:click="cancelRestoreSoftDeleted({{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="cancelar creación">&times;</span>
                    </div>
                  @endforeach

                  {{-- packs a eliminar --}}
                  @foreach ($packs_to_delete as $key => $pack )
                    <div class="flex items-center justify-start gap-1 border border-red-300 bg-red-200 py-px px-1 rounded-lg">
                      <span class="text-xs uppercase font-semibold">eliminar</span>
                      <span class="text-sm text-neutral-600 lowercase">pack de {{ $pack->pack_units }}</span>
                      <span wire:click="cancelPackElimination({{ $pack->id }}, {{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="cancelar eliminación">&times;</span>
                    </div>
                  @endforeach

                  @if (count($packs) == 0 && count($new_packs) == 0 && count($packs_to_delete) == 0)
                    <span class="text-sm text-neutral-600">no se crearán packs de este suministro ...</span>
                  @endif


                </div>
              </div>

            </div>

          </x-fieldset-base>

          <!-- botones del formulario -->
          <div class="flex justify-end my-2 gap-2">

            <x-a-button
              wire:navigate
              href="{{ route('suppliers-provisions-index') }}"
              bg_color="neutral-600"
              border_color="neutral-600"
              >cancelar
            </x-a-button>

            <x-btn-button>guardar</x-btn-button>

          </div>

        </form>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
