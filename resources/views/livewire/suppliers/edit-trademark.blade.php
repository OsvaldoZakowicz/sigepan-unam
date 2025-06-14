<div>
  {{-- componente crear marca--}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="editar marca"></x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <form  wire:submit="save" class="w-full flex flex-col gap-2">

          <x-fieldset-base tema="marca" class="w-full lg:flex-nowrap">

            {{-- nombre de la marca --}}
            <div class="flex flex-col gap-1 p-2 w-full md:w-1/2">
              <span>
                <x-input-label
                  for="provision_trademark_name"
                  class="font-normal"
                  >nombre
                </x-input-label>
                <span class="text-red-600">*</span>
                @error('provision_trademark_name')
                  <span class="text-red-400 text-xs">{{ $message }}</span>
                @enderror
              </span>
              <x-text-input
                wire:model="provision_trademark_name"
                name="provision_trademark_name"
                id="provision_trademark_name"
              />
            </div>

          </x-fieldset-base>

          <!-- botones del formulario -->
          <div class="flex justify-end my-2 gap-2">
            <x-a-button wire:navigate href="{{ route('suppliers-trademarks-index') }}" bg_color="neutral-600" border_color="neutral-600">cancelar</x-a-button>

            <x-btn-button>guardar</x-btn-button>
          </div>

        </form>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
