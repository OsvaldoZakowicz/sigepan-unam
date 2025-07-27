<div>
  {{-- componente crear categoria de suministros--}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear categoria de suministros"></x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <form  wire:submit="save" wire:confirm="¿crear categoría?" class="flex flex-col w-full gap-2">

          <x-fieldset-base tema="categoria de suministros" class="w-full lg:flex-nowrap">

            {{-- nombre de la categoria --}}
            <div class="flex flex-col w-full gap-1 p-2 md:w-1/3">
              <span>
                <x-input-label for="provision_category_name" class="font-normal">nombre</x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_category_name') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
              <x-text-input wire:model="provision_category_name" name="provision_category_name" id="provision_category_name" />
            </div>

            <div class="flex flex-col w-full gap-1 p-2 md:w-1/3">
              <span>
                <x-input-label for="measure_id" class="font-normal">medida de la categoría</x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('measure_id') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
              <select
                wire:model="measure_id"
                name="measure_id"
                id="measure_id"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                  <option value="">seleccione una medida ...</option>
                @forelse ($measures as $measure)
                  <option value="{{ $measure->id }}">{{ $measure->unit_name }}{{ $measure->conversion_unit ? ' y ' . $measure->conversion_unit : '' }}</option>
                @empty
                  <option value="">sin medidas disponibles</option>
                @endforelse
              </select>
            </div>

            <div class="flex flex-col w-full gap-1 p-2 md:w-1/3">
              <span>
                <x-input-label for="provision_type_id" class="font-normal">tipo de la categoría</x-input-label>
                <span class="text-red-600">*</span>
              </span>
              @error('provision_type_id') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
              <select
                wire:model="provision_type_id"
                name="provision_type_id"
                id="provision_type_id"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                  <option value="">seleccione el tipo ...</option>
                @forelse ($types as $type)
                  <option value="{{ $type->id }}">{{ $type->provision_type_name }}</option>
                @empty
                  <option value="">Sin tipos disponibles</option>
                @endforelse
              </select>
            </div>

          </x-fieldset-base>

          <!-- botones del formulario -->
          <div class="flex justify-end gap-2 my-2">

            <x-a-button
              wire:navigate
              href="{{ route('suppliers-categories-index') }}"
              bg_color="neutral-600"
              border_color="neutral-600"
              >cancelar
            </x-a-button>

            <x-btn-button
              >guardar
            </x-btn-button>

          </div>

        </form>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
