<div>
  {{-- componente crear suministro --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear suministro"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <form wire:submit="save" class="w-full flex flex-col gap-2">

          <x-fieldset-base tema="suministro individual" class="w-full">

            {{-- categoria --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="provision_category_id"
                  class="font-normal"
                  >categoria
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_category_id')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <select
                name="provision_category_id"
                wire:model.live="provision_category_id"
                id="provision_category_id"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

                <option value="" selected>seleccione una categoria ...</option>

                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" class="cursor-pointer">
                    {{ $category->provision_category_name }}
                  </option>
                @endforeach

              </select>
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
                wire:model.live="provision_trademark_id"
                id="provision_trademark_id"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">

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
                  for="provision_type"
                  class="font-normal"
                  >tipo
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_type')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <input
                type="text"
                name="provision_type"
                wire:model="provision_type"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-200"
                readonly
              />
            </div>

            {{-- unidad de medida --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="measure"
                  class="font-normal"
                  >unidad de medida
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('measure')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              <input
                type="text"
                name="measure"
                wire:model="measure"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-200"
                readonly
              />
            </div>

            {{-- cantidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label
                  for="provision_quantity"
                  class="font-normal"
                  >cantidad
                </x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_quantity')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
              @if ($measure === 'unidad')
                <x-text-input
                  wire:model="provision_quantity"
                  name="provision_quantity"
                  value="1"
                  placeholder="1"
                  class="p-1 text-end text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-200"
                  readonly
                />
              @else
                <x-text-input
                  wire:model="provision_quantity"
                  name="provision_quantity"
                  placeholder="{{ $input_quantity_placeholder }}"
                  class="text-right"
                />
              @endif
            </div>

            {{-- descripcion --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:grow">
              <span>
                <x-input-label
                  for="provision_short_description"
                  class="font-normal"
                  >descripcion
                </x-input-label>
                @error('provision_short_description')
                  <span class="text-red-400 text-xs">{{ $message }}</span>
                @enderror
              </span>
              <x-text-input wire:model="provision_short_description" name="provision_short_description" />
            </div>

          </x-fieldset-base>

          <x-fieldset-base tema="packs del suministro" class="w-full">

            {{-- pack --}}
            <div class="flex justify-start items-end p-2 w-full">

              {{-- indicar cantidad del pack --}}
              <div class="flex flex-col gap-1 pr-2">
                <label for="pack_units">crear packs del suministro</label>
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
                    <option value="100">pack de 100 (recomendado para insumos)</option>
                    <option value="250">pack de 250 (recomendado para insumos)</option>
                    <option value="500">pack de 500 (recomendado para insumos)</option>
                    <option value="1000">pack de 1000 (recomendado para insumos)</option>
                  </select>
                  <x-a-button
                    href="#"
                    wire:click="addPackUnits()"
                    bg_color="neutral-100"
                    border_color="neutral-100"
                    text_color="neutral-600"
                    >seleccionar
                  </x-a-button>
                </div>
              </div>

              {{-- mostrar packs a crear --}}
              <div class="flex flex-col md:w-1/2 lg:grow">
                <span class="ml-2">lista de packs</span>
                <div class="flex justify-start items-center gap-1 ml-2 p-1 h-8 border border-dashed border-neutral-200 leading-none">
                  @forelse ($packs as $key => $pack)
                    <div class="flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 py-px px-1 rounded-lg">
                      <span class="text-xs uppercase font-semibold">crear</span>
                      <span class="text-sm text-neutral-600 lowercase">pack de {{ $pack }}</span>
                      <span wire:click="removePackUnits({{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="cancelar creación">&times;</span>
                    </div>
                  @empty
                    <span class="text-sm text-neutral-600">no se crearán packs de este suministro ...</span>
                  @endforelse
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
