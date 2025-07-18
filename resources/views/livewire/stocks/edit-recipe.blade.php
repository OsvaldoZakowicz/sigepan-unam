<div>
  {{-- componente editar receta --}}
  {{-- componente crear receta --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="editar receta"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <form class="flex flex-col w-full gap-1">

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

            <div class="flex items-start justify-start gap-4">
              {{-- columna de inputs 1 --}}
              <div class="flex flex-col w-1/2 gap-4">

                <div class="flex gap-4 min-h-fit">
                  {{-- producto de la receta --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2">
                    <span>
                      <label for="product_name">producto de la receta</label>
                    </span>
                    <input 
                      type="text" 
                      name="product_name"
                      id="product_name"
                      value="{{ $recipe->product->product_name }}"
                      @readonly(true)
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-200"
                      />
                  </div>
                  {{-- titulo de la receta --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2">
                    <span>
                      <label for="recipe_title">titulo de la receta</label>
                      <x-quest-icon title="generado de forma automática" />
                    </span>
                    @error('recipe_title')
                      <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                    <input
                      name="recipe_title"
                      id="recipe_title"
                      value="{{ $recipe->recipe_title }}"
                      @readonly(true)
                      type="text"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-200"
                    />
                  </div>
                </div>

                <div class="flex gap-4 min-h-fil">
                  {{-- rendimiento en unidades de la receta --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2">
                    <span>
                      <label for="recipe_yields">rendimiento en unidades</label>
                      <span class="text-red-600">*</span>
                      <x-quest-icon title="cantidad de productos que resultan de preparar la receta" />
                    </span>
                    @error('recipe_yields')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      name="recipe_yields"
                      id="recipe_yields"
                      wire:model.live="recipe_yields"
                      type="text"
                      class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>
                  {{-- rendimiento en porciones de la receta --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2">
                    <span>
                      <label for="recipe_portions">porciones por unidad</label>
                      <span class="text-red-600">*</span>
                      <x-quest-icon title="cantidad de porciones por cada producto elaborado" />
                    </span>
                    @error('recipe_portions')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      type="text"
                      wire:model.live="recipe_portions"
                      name="recipe_portions"
                      id="recipe_portions"
                      class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>
                </div>

                <div class="flex gap-4 min-h-fil">
                  {{-- tiempo de preparacion de la receta --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2">
                    <span>
                      <label for="time">tiempo de preparación, horas y minutos (HH:MM)</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('time')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    {{-- horas --}}
                    <div class="flex items-end justify-start gap-1">
                      <input
                        type="text"
                        name="time"
                        wire:model.live="time"
                        placeholder="HH:MM"
                        class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                      <span>hrs</span>
                    </div>
                  </div>
                </div>
              </div>
              {{-- columna de inputs 2 --}}
              <div class="flex flex-col w-1/2 gap-4">

                <div class="flex min-h-fit">
                  {{-- instrucciones de preparacion corta --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit">
                    <span>
                      <label for="recipe_instructions">instrucciones de preparación</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('recipe_instructions')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <textarea
                      wire:model.live="recipe_instructions"
                      name="recipe_instructions"
                      id="recipe_instructions" 
                      rows="5" 
                      cols="10" 
                      class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                    </textarea>
                  </div>
                </div>

              </div>
            </div>

          </x-div-toggle>

          <x-div-toggle x-data="{ open: true }" title="suministros de la receta" class="w-full p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">edite los suministros que necesita para la receta</span>
            </x-slot:subtitle>

            @error('provision_categories*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            {{-- componente de busqueda de suministros para las recetas --}}
            @livewire('stocks.search-provision-recipe')

            {{-- lista de suministros para la rceta --}}
            <div class="flex flex-col w-full gap-1">

              {{-- leyenda --}}
              <div class="py-1">
                <span class="font-semibold capitalize">lista de suministros de la receta</span>
                @error('provision_categories')<span class="text-red-400">{{ $message }}</span>@enderror
              </div>

              <div class="overflow-x-hidden overflow-y-auto max-h-60">
                <x-table-base>
                  <x-slot:tablehead>
                    <tr class="border bg-neutral-100">
                      <x-table-th class="w-12 text-end">
                        id
                      </x-table-th>
                      <x-table-th class="text-start">
                        nombre
                      </x-table-th>
                      <x-table-th class="text-start">
                        tipo
                      </x-table-th>
                      <x-table-th class="text-end">
                        <span>cantidad necesaria</span>
                        <x-quest-icon title="kilogramos (kg), gramos (g), litros (L), mililitros (ml), metros (m), centimetros (cm)  o unidades (un)"/>
                      </x-table-th>
                      <x-table-th class="w-16 text-start">
                        quitar
                      </x-table-th>
                    </tr>
                  </x-slot:tablehead>
                  <x-slot:tablebody>
                    @forelse ($provision_categories as $key => $category)
                      <tr class="border">
                        <x-table-td class="text-end">
                          {{ $category['category']->id }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $category['category']->provision_category_name }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $category['category']->provision_type->provision_type_name }}
                        </x-table-td>
                        {{-- cantidad necesaria --}}
                        <x-table-td class="text-end">
                          {{-- cantidad --}}
                          @error('provision_categories.'.$key.'.quantity')
                            <span class="text-xs text-red-400">{{ $message }}</span>
                          @enderror
                          <div class="flex items-center justify-end gap-2">
                            <input
                              type="text"
                              id="provision_categories_{{ $key }}_quantity"
                              wire:model.defer="provision_categories.{{ $key }}.quantity"
                              placeholder="cantidad"
                              class="w-48 p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                            />
                            <span class="capitalize">({{ $category['category']->measure->unit_symbol }})</span>
                            @if ($category['category']->measure->conversion_unit !== null)
                              <span>o, ({{ $category['category']->measure->conversion_symbol ?? null }})</span>
                            @endif
                          </div>
                        </x-table-td>
                        {{-- acciones --}}
                        <x-table-td class="text-start">
                          {{-- quitar item de la lista --}}
                          <div class="inline-flex items-center justify-start w-full gap-1">
                            <span
                              wire:click="removeItemFromList({{ $key }})"
                              title="quitar de la lista"
                              class="p-1 font-bold leading-none text-center bg-red-100 border border-red-200 rounded-sm cursor-pointer"
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
              <div class="flex items-center justify-end w-full gap-2 mt-2">
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
        <div class="flex justify-end gap-2 my-2">

          <x-a-button
            wire:navigate
            href="{{ route('stocks-recipes-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            wire:confirm="¿Desea editar la receta?"
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
