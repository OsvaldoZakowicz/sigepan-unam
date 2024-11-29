<div>
  {{-- componente crear periodo de peticion presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear periodo de petición de presupuestos"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        {{-- <span class="mb-2 font-bold">formulario</span> --}}

        <form wire:submit="save()" class="w-full flex flex-col gap-2">

          <x-fieldset-base tema="periodo de solicitud" class="w-full">

            {{-- fecha de inicio --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="period_start_at" class="font-normal">fecha de inicio</x-input-label>
                <span class="text-red-600">*</span>
                @error('period_start_at') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <input
                type="date"
                name="period_start_at"
                id="period_start_at"
                wire:model="period_start_at"
                min="{{ $min_date }}"
                max="{{ $max_date }}"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
            </div>

            {{-- fecha de cierre --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
              <span>
                <x-input-label for="period_end_at" class="font-normal">fecha de cierre</x-input-label>
                <span class="text-red-600">*</span>
                @error('period_end_at') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <input
                type="date"
                name="period_end_at"
                id="period_end_at"
                wire:model="period_end_at"
                min="{{ $min_date }}"
                max="{{ $max_date }}"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
            </div>

            {{-- descripcion --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:grow">
              <span>
                <x-input-label for="period_short_description" class="font-normal">descripcion corta</x-input-label>
                @error('period_short_description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
              </span>
              <input
                type="text"
                name="period_short_description"
                id="period_short_description"
                wire:model="period_short_description"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
            </div>

          </x-fieldset-base>

          <!-- botones del formulario -->
          <div class="flex justify-end my-2 gap-2">

            <x-a-button
              wire:navigate
              href="{{ route('suppliers-budgets-periods-index') }}"
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
