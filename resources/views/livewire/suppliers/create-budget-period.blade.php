<div>
  {{-- componente crear periodo de peticion presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear periodo de petición de presupuestos"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        {{-- formulario del periodo --}}
        <form class="flex flex-col w-full gap-1">

          {{-- datos del periodo --}}
          <x-div-toggle x-data="{open: true}" title="datos del período" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">Establezca el dia en que abrirá y cerrará el periodo.</span>
            </x-slot:subtitle>

            @error('period_*')
            <x-slot:messages class="my-2">
              <span class="text-red-400">¡hay errores en esta seccion!</span>
            </x-slot:messages>
            @enderror

            {{-- subgrupo --}}
            <div class="flex gap-1 min-h-fit">
              {{-- fecha de inicio --}}
              <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2 lg:w-1/4">
                <span>
                  <x-input-label for="period_start_at" class="font-normal">fecha de inicio</x-input-label>
                  <span class="text-red-600">*</span>
                  @error('period_start_at') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </span>
                <input type="date" name="period_start_at" id="period_start_at" wire:model="period_start_at"
                  min="{{ $min_date }}" max="{{ $max_date }}"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>

              {{-- fecha de cierre --}}
              <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2 lg:w-1/4">
                <span>
                  <x-input-label for="period_end_at" class="font-normal">fecha de cierre</x-input-label>
                  <span class="text-red-600">*</span>
                  @error('period_end_at') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </span>
                <input type="date" name="period_end_at" id="period_end_at" wire:model="period_end_at"
                  min="{{ $min_date }}" max="{{ $max_date }}"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>

              {{-- descripcion --}}
              <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2 lg:grow">
                <span>
                  <x-input-label for="period_short_description" class="font-normal">descripcion corta</x-input-label>
                  @error('period_short_description') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </span>
                <input type="text" name="period_short_description" id="period_short_description"
                  wire:model="period_short_description"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>
            </div>

          </x-div-toggle>

          {{-- buscar suministros para presupuestar --}}
          <x-div-toggle x-data="{open: true}" title="suministros y packs a presupuestar" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">Se presupuestarán suministros y packs de proveedores <x-text-tag
                  color="emerald">activos</x-text-tag></span>
            </x-slot:subtitle>

            @error('provisions_and_packs*')
            <x-slot:messages class="my-2">
              <span class="text-red-400">¡hay errores en esta seccion!</span>
            </x-slot:messages>
            @enderror

            {{-- cuadro de busqueda --}}
            @livewire('suppliers.search-provision-period', ['is_editing' => false])

            {{-- leyenda --}}
            <div class="py-1">
              <span class="text-sm text-neutral-600">Lista de suministros y packs a presupuestar.</span>
              @error('provisions_and_packs')
              <span class="text-xs text-red-400">{{ $message }}</span>
              @enderror
            </div>

            {{-- lista de seleccion, con scroll --}}
            <div class="overflow-x-hidden overflow-y-scroll max-h-60">
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
                      marca
                    </x-table-th>
                    <x-table-th class="text-start">
                      tipo
                    </x-table-th>
                    <x-table-th class="text-end">
                      <span>volumen</span>
                      <x-quest-icon
                        title="kilogramos (Kg), gramos (g), litros (L), mililitros (mL), metros (M), centimetros (cm) o unidades (U)" />
                    </x-table-th>
                    <x-table-th class="text-end">
                      <span>cantidad a presupuestar</span>
                      <span class="text-red-400">*</span>
                      <x-quest-icon
                        title="cantidad de unidades de cada pack o de cada suministro que desea presupuestar" />
                    </x-table-th>
                    <x-table-th class="w-16 text-start">quitar</x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @forelse ($provisions_and_packs as $key => $item )
                  <tr wire:key="{{ $key }}" class="border">
                    @if ($item['item_type'] === 'suministro')
                      {{-- suministro --}}
                      <x-table-td class="w-12 text-end">
                        {{ $item['item_object']->id }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $item['item_object']->provision_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $item['item_object']->trademark->provision_trademark_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $item['item_object']->type->provision_type_name }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ convert_measure($item['item_object']->provision_quantity, $item['item_object']->measure) }}
                      </x-table-td>
                      <x-table-td class="w-56 text-end">
                        {{-- cantidad --}}
                        @error('provisions_and_packs.'.$key.'.item_quantity')
                          <span class="text-xs text-red-400">{{ $message }}</span>
                        @enderror
                        <input
                          type="number"
                          id="provisions_and_packs_{{ $key }}_item_quantity"
                          min="1"
                          max="99"
                          wire:model.defer="provisions_and_packs.{{ $key }}.item_quantity" placeholder="cantidad"
                          class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                        />
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{-- quitar --}}
                        <div class="inline-flex items-center justify-start w-full gap-1">
                          <span
                            wire:click="removeItemFromList({{ $key }})"
                            title="quitar de la lista"
                            class="p-1 font-bold leading-none text-center bg-red-100 border border-red-200 rounded-sm cursor-pointer">&times;
                          </span>
                        </div>
                      </x-table-td>
                    @else
                      {{-- pack --}}
                      <x-table-td class="w-12 text-end">
                        {{ $item['item_object']->id }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $item['item_object']->pack_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $item['item_object']->provision->trademark->provision_trademark_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $item['item_object']->provision->type->provision_type_name }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ convert_measure($item['item_object']->pack_quantity, $item['item_object']->provision->measure)
                        }}
                      </x-table-td>
                      <x-table-td class="w-56 text-end">
                        {{-- cantidad --}}
                        @error('provisions_and_packs.'.$key.'.item_quantity')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                        @enderror
                        <input type="number" id="provisions_and_packs_{{ $key }}_item_quantity" min="1" max="99"
                          wire:model.defer="provisions_and_packs.{{ $key }}.item_quantity" placeholder="cantidad"
                          class="w-full p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{-- quitar --}}
                        <div class="inline-flex items-center justify-start w-full gap-1">
                          <span wire:click="removeItemFromList({{ $key }})" title="quitar de la lista"
                            class="p-1 font-bold leading-none text-center bg-red-100 border border-red-200 rounded-sm cursor-pointer">&times;
                          </span>
                        </div>
                      </x-table-td>
                    @endif
                  </tr>
                  @empty
                  <tr class="border">
                    <td colspan="7">
                      <span>¡sin registros!</span>
                    </td>
                  </tr>
                  @endforelse
                </x-slot:tablebody>
              </x-table-base>
            </div>

            {{-- acciones de lista --}}
            <div class="flex items-center justify-end w-full gap-2 mt-2">
              <x-a-button href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600"
                wire:click="removeAllItemsFromList()">vaciar lista
              </x-a-button>
            </div>

          </x-div-toggle>

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">
        <!-- botones del formulario -->
        <div class="flex justify-end gap-2 my-2">

          <x-a-button wire:navigate href="{{ route('suppliers-budgets-periods-index') }}" bg_color="neutral-600"
            border_color="neutral-600">cancelar
          </x-a-button>

          <x-btn-button type="button" wire:click="save()"
            wire:confirm="¿Crear periodo?, una vez creado no podrá modificarlo. Si la fecha de inicio es el dia de hoy, el periodo abrira inmediatamente y se contactará a los proveedores activos.">
            guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>