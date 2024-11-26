<div>
  {{-- componente crear receta --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear receta"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        {{-- todo: busquedas de ingredientes e insumos --}}
        {{-- @livewire('stocks.search-provision-recipe', ['recipe_id' => null, 'is_editing' => false]) --}}

        {{-- todo: formulario de alta con lista de ingredientes e insumos --}}
        <div class="flex flex-col gap-1 w-full">
          <span class="font-semibold capitalize">formulario: descripción de la receta.</span>

          <form wire:submit="save()">

            <x-fieldset-base tema="comercio o empresa" class="w-full">

              {{-- titulo de la receta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="recipe_title">titulo de la receta</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_title')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="recipe_title" type="text" name="recipe_title" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>

              {{-- rendimiento en unidades de la receta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="recipe_yields">rendimiento en unidades</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_yields')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="recipe_yields" type="text" name="recipe_yields" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
              </div>

              {{-- rendimiento en porciones de la receta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="recipe_portions">rendimiento en porciones</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_portions')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="recipe_portions" type="text" name="recipe_portions" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
              </div>

              {{-- tiempo de preparacion de la receta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="recipe_preparation_time">tiempo de preparación (minutos)</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_preparation_time')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="recipe_preparation_time" type="number" min="1" max="720" step="1" name="recipe_preparation_time" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
              </div>

              {{-- instrucciones de preparacion corta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-full">
                <span>
                  <label for="recipe_instructions">instrucciones de preparación</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_instructions')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <textarea wire:model="recipe_instructions" name="recipe_instructions" rows="2" cols="10" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
              </div>

              {{-- descripcion corta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-full">
                <span>
                  <label for="recipe_short_description">notas adicionales</label>
                  @error('recipe_short_description')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <textarea wire:model="recipe_short_description" name="recipe_short_description" rows="2" cols="10" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
              </div>

            </x-fieldset-base>

            <div class="flex w-full justify-end items-center gap-2 mb-2">
              <x-a-button wire:navigate href="{{ route('stocks-recipes-index') }}" bg_color="neutral-600" border_color="neutral-600" text_color="neutral-100">cancelar</x-a-button>
              <x-btn-button>guardar receta</x-btn-button>
            </div>

          </form>

        </div>

        {{-- lista de suministros elegidos --}}
        {{-- <div class="flex flex-col gap-1 w-full">
          <span class="font-semibold capitalize">formulario: lista de ingredientes e insumos necesarios.</span>
          <div class="max-h-72 overflow-y-auto overflow-x-hidden">
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th>id</x-table-th>
                  <x-table-th>suministro</x-table-th>
                  <x-table-th>$&nbsp;cantidad necesaria<span class="text-red-400">*</span></x-table-th>
                  <x-table-th>acciones</x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                @forelse ($provisions as $provision_array_key => $provision_id)

                @empty
                  <tr class="border">
                    <td colspan="4">¡lista vacia!, búsque y agregue suministros a esta lista para comenzar.</td>
                  </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
          </div>
        </div> --}}

      </x-slot:content>

      <x-slot:footer class="hidden py-2">
        <!-- grupo de botones -->
        <div class="flex gap-2">
        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
