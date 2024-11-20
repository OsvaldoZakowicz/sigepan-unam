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
        @livewire('stocks.search-provision-recipe', ['recipe_id' => null, 'is_editing' => false])

        {{-- todo: formulario de alta con lista de ingredientes e insumos --}}
        <div class="flex flex-col gap-1 w-full">
          <span class="font-semibold capitalize">formulario: descripción de la receta.</span>

          <form>

            <x-fieldset-base tema="comercio o empresa" class="w-full">
              {{-- nombre de la receta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="recipe_name">nombre de la receta</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_name')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="recipe_name" type="text" name="recipe_name" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>
              {{-- rendimiento de la receta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="recipe_production">rendimiento de la receta</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_production')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="recipe_production" type="text" name="recipe_production" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
              </div>
              {{-- descripcion corta --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-full">
                <span>
                  <label for="recipe_short_description">descripcion de la receta, o modo de preparación</label>
                  <span class="text-red-600">*</span>
                  @error('recipe_short_description')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <textarea wire:model="recipe_short_description" name="recipe_short_description" rows="2" cols="10" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
              </div>
            </x-fieldset-base>

          </form>

        </div>

        {{-- lista de suministros elegidos --}}
        <div class="flex flex-col gap-1 w-full">
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
                  {{-- todo: componente InputProvisionForm para cantidad de suministro --}}
                @empty
                  <tr class="border">
                    <td colspan="4">¡lista vacia!, búsque y agregue suministros a esta lista para comenzar.</td>
                  </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
          </div>
        </div>

      </x-slot:content>

      <x-slot:footer class="py-2">
        <!-- grupo de botones -->
        <div class="flex gap-2">
          <x-a-button wire:navigate href="#" wire:click="refresh" bg_color="neutral-100" border_color="neutral-300" text_color="neutral-600">vaciar lista</x-a-button>
          <x-btn-button type="button" wire:click="save" >guardar receta</x-btn-button>
        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
