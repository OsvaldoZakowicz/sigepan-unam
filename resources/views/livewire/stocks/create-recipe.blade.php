<div>
  {{-- componente crear receta --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear receta"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        {{-- formulario del periodo --}}
        <form class="w-full flex flex-col gap-1">

          <x-div-toggle x-data="{ open: true }" title="detalles de la receta" class="w-full p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">complete los detalles principales de la receta</span>
            </x-slot:subtitle>

            @error('recipe_*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            <div class="flex flex-col gap-4">
              <div class="flex gap-4 min-h-fit">
                {{-- producto de la receta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                  <span>
                    <label for="">producto de la receta</label>
                    <span class="text-red-600">*</span>
                  </span>
                  <select
                    name="product_id"
                    wire:model="product_id"
                    class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    >
                    <option value="">seleccione un producto ...</option>
                    @foreach ($products as $product)
                      <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                  </select>
                </div>
                {{-- titulo de la receta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                  <span>
                    <label for="recipe_title">titulo de la receta</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('recipe_title')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <input
                    name="recipe_title"
                    id="recipe_title"
                    wire:model="recipe_title"
                    type="text"
                    class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  />
                </div>
              </div>

              <div class="flex gap-4 min-h-fil">
                {{-- rendimiento en unidades de la receta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2 lg:w-1/3">
                  <span>
                    <label for="recipe_yields">rendimiento en unidades</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('recipe_yields')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <input
                    name="recipe_yields"
                    id="recipe_yields"
                    wire:model="recipe_yields"
                    type="text"
                    class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  />
                </div>
                {{-- rendimiento en porciones de la receta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2 lg:w-1/3">
                  <span>
                    <label for="recipe_portions">porciones por unidad</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('recipe_portions')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <input
                    type="text"
                    wire:model="recipe_portions"
                    name="recipe_portions"
                    id="recipe_portions"
                    class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  />
                </div>
                {{-- tiempo de preparacion de la receta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2 lg:w-1/3">
                  <span>
                    <label for="">tiempo de preparación, horas y minutos</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('time_h')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  @error('time_m')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <div class="flex gap-1 items-center justify-start">
                    {{-- horas --}}
                    <input
                      type="number"
                      wire:model.live="time_h"
                      min="0"
                      max="12"
                      step="1"
                      placeholder="HH"
                      class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                    <span>horas&nbsp;:&nbsp;</span>
                    {{-- minutos --}}
                    <input
                    type="number"
                    wire:model.live="time_m"
                    min="1"
                    max="59"
                    step="1"
                    placeholder="mm"
                    class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                    <span>minutos</span>
                  </div>
                </div>
              </div>

              <div class="flex min-h-fit">
                {{-- instrucciones de preparacion corta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full">
                  <span>
                    <label for="recipe_instructions">instrucciones de preparación</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('recipe_instructions')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <textarea wire:model="recipe_instructions" name="recipe_instructions" rows="2" cols="10" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
                </div>
              </div>
            </div>


          </x-div-toggle>

          <x-div-toggle x-data="{ open: true }" title="suministros de la receta" class="w-full p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">elija los suministros que necesita para la receta</span>
            </x-slot:subtitle>

            @error('provisions*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            {{-- componente de busqueda de suministros para las recetas --}}
            @livewire('stocks.search-provision-recipe', ['recipe_id' => null, 'is_editing' => false])

            {{-- lista de suministros para la rceta --}}
            <div class="flex flex-col gap-1 w-full">

              {{-- leyenda --}}
              <div class="py-1">
                <span class="font-semibold capitalize">lista de suministros de la receta</span>
                @error('provisions')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
              </div>

              <div class="max-h-60 overflow-y-auto overflow-x-hidden">
                <x-table-base>
                  <x-slot:tablehead>
                    <tr class="border bg-neutral-100">
                      <x-table-th class="text-end w-12">id</x-table-th>
                      <x-table-th class="text-start">nombre</x-table-th>
                      <x-table-th class="text-start">marca</x-table-th>
                      <x-table-th class="text-start">tipo</x-table-th>
                      <x-table-th class="text-end">
                        <span>cantidad necesaria</span>
                        <x-quest-icon title="kilogramos (kg), litros (lts) o unidades (un)"/>
                      </x-table-th>
                      <x-table-th class="text-start w-16">quitar</x-table-th>
                    </tr>
                  </x-slot:tablehead>
                  <x-slot:tablebody>
                    @forelse ($provisions as $key => $provision)
                      <tr class="border">
                        <x-table-td class="text-end">
                          {{ $provision['provision']->id }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $provision['provision']->provision_name }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $provision['provision']->trademark->provision_trademark_name }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $provision['provision']->type->provision_type_name }}
                        </x-table-td>
                        {{-- cantidad necesaria --}}
                        <x-table-td class="text-end">
                          {{-- cantidad --}}
                          @error('provisions.'.$key.'.quantity')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                          @enderror
                          <input
                            type="text"
                            id="provisions_{{ $key }}_quantity"
                            wire:model.defer="provisions.{{ $key }}.quantity"
                            placeholder="cantidad"
                            class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                            />
                        </x-table-td>
                        {{-- acciones --}}
                        <x-table-td class="text-start">
                          <div class="w-full inline-flex gap-1 justify-start items-center">
                            <span
                              wire:click="removeItemFromList({{ $key }})"
                              title="quitar de la lista"
                              class="font-bold leading-none text-center p-1 cursor-pointer bg-red-100 border border-red-200 rounded-sm"
                              >&times;
                            </span>
                          </div>
                        </x-table-td>
                      </tr>
                    @empty
                      <tr class="border">
                        <td colspan="7">¡lista vacia!</td>
                      </tr>
                    @endforelse
                  </x-slot:tablebody>
                </x-table-base>
              </div>
              {{-- acciones de lista --}}
              <div class="w-full flex justify-end items-center gap-2 mt-2">
                <x-a-button
                  href="#"
                  bg_color="neutral-100"
                  border_color="neutral-200"
                  text_color="neutral-600"
                  wire:click="removeAllItemsFromList()"
                  >vaciar lista
                </x-a-button>
              </div>
            </div>

          </x-div-toggle>

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">
        <!-- botones del formulario -->
        <div class="flex justify-end my-2 gap-2">

          <x-a-button
            wire:navigate
            href="{{ route('suppliers-budgets-periods-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
