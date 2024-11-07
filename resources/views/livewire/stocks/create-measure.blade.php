<div>
  {{-- componente crear unidad de medida--}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear unidad de medida"></x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <span class="mb-2 font-bold">formulario</span>

        <form  wire:submit="save" class="w-full flex flex-col gap-2">

          <x-fieldset-base tema="unidad de medida" class="w-full lg:flex-nowrap">

            {{-- nombre de la unidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="measure_name" class="font-normal">nombre de unidad</x-input-label>
                <span class="text-red-600">*</span>
                @error('measure_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="measure_name" name="measure_name" id="measure_name" />
            </div>
            {{-- abreviatura de la unidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="measure_abrv" class="font-normal">abreviatura de unidad</x-input-label>
                <span class="text-red-600">*</span>
                @error('measure_abrv') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="measure_abrv" name="measure_abrv" id="measure_abrv" />
            </div>
            {{-- cantidad base de la unidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="measure_base" class="font-normal">cantidad base</x-input-label>
                <span class="text-red-600">*</span>
                @error('measure_base') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="measure_base" name="measure_base" id="measure_base" />
            </div>
            {{-- descripcion corta de la unidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="measure_short_description" class="font-normal">descripcion corta</x-input-label>
                @error('measure_short_description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="measure_short_description" name="measure_short_description" id="measure_short_description" />
            </div>

          </x-fieldset-base>

          <!-- botones del formulario -->
          <div class="flex justify-end my-2 gap-2">
            <x-a-button wire:navigate href="{{ route('stocks-measures-index') }}" bg_color="neutral-600" border_color="neutral-600">cancelar</x-a-button>

            <x-btn-button>guardar</x-btn-button>
          </div>

        </form>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
