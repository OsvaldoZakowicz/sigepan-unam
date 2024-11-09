<div>
  {{-- componente crear suministro --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear suministro"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <span class="mb-2 font-bold">formulario</span>

        <form wire:submit="save" class="w-full flex flex-col gap-2">

          <x-fieldset-base tema="suministro individual" class="w-full">

            {{-- nombre --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="provision_name" class="font-normal">nombre del suministro</x-input-label>
                <span class="text-red-600">*</span>
                @error('provision_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="provision_name" name="provision_name" />
            </div>

            {{-- marca --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="provision_trademark_id" class="font-normal">marca</x-input-label>
                <span class="text-red-600">*</span>
                @error('provision_trademark_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <select name="provision_trademark_id" wire:model="provision_trademark_id" id="provision_trademark_id" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                <option value="" selected>seleccione una marca ...</option>
                @foreach ($trademarks as $trademark)
                  <option value="{{ $trademark->id }}">{{ $trademark->provision_trademark_name }}</option>
                @endforeach
              </select>
            </div>

            {{-- tipo --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="provision_type_id" class="font-normal">tipo</x-input-label>
                <span class="text-red-600">*</span>
                @error('provision_type_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <select name="provision_type_id" wire:model="provision_type_id" id="provision_type_id" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                <option value="" selected>seleccione un tipo ...</option>
                @foreach ($provision_types as $type)
                  <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
                @endforeach
              </select>
            </div>

            {{-- unidad de medida --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="measure_id" class="font-normal">unidad de medida</x-input-label>
                <span class="text-red-600">*</span>
                @error('measure_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <select name="measure_id" wire:model="measure_id" id="measure_id" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                <option value="" selected>seleccione una unidad ...</option>
                @foreach ($measures as $measure)
                  <option value="{{ $measure->id }}" title="{{ $measure->measure_short_description }}" class="cursor-pointer">
                    {{ $measure->measure_name }}({{ $measure->measure_abrv }})
                  </option>
                @endforeach
              </select>
            </div>

            {{-- cantidad --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="provision_quantity" class="font-normal">cantidad, volumen o peso: {{ $measure_id }}</x-input-label>
                <span class="text-red-600">*</span>
                @error('provision_quantity') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="provision_quantity" name="provision_quantity" />
            </div>

            {{-- descripcion --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:grow">
              <span>
                <x-input-label for="provision_short_description" class="font-normal">descripcion</x-input-label>
                @error('provision_short_description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <x-text-input wire:model="provision_short_description" name="provision_short_description" />
            </div>

            {{-- crear pack --}}
            {{-- <div class="flex flex-col items-start justify-center gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <x-input-label for="provision_short_description" class="font-normal">crear pack del suministro?</x-input-label>
              <input type="checkbox" wire:click="togglePackForm()" name="show_pack_form" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div> --}}

          </x-fieldset-base>

          {{-- todo: si es pack, cargar pack --}}
          {{-- @if ($show_pack_form)
            <x-fieldset-base tema="pack del suministro" class="w-full">
              crear pack
            </x-fieldset-base>
          @endif --}}

          <!-- botones del formulario -->
          <div class="flex justify-end my-2 gap-2">
            <x-a-button wire:navigate href="{{ route('suppliers-provisions-index') }}" bg_color="neutral-600" border_color="neutral-600">cancelar</x-a-button>

            <x-btn-button>guardar</x-btn-button>
          </div>

        </form>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

    </article>
</div>
