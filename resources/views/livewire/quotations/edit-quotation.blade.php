<div>
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="modificar peticion de presupuesto">
      <x-slot:title>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">modificar peticion de presupuestos</span>
      </x-slot:title>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- estado --}}
        <div class="flex items-center justify-start gap-2">
          {{-- estado de pre orden --}}
          @if ($quotation->is_completed)
          <x-text-tag color="emerald" class="cursor-pointer">respondido
            <x-quest-icon title="el proveedor ha respondido a este presupuesto" />
          </x-text-tag>
          @else
          <x-text-tag color="neutral" class="cursor-pointer">sin responder
            <x-quest-icon title="el proveedor aún no responde a este presupuesto" />
          </x-text-tag>
          @endif
        </div>
      </x-slot:header>

      <x-slot:content class="flex-col overflow-y-auto">
        <section>
          {{-- encabezado --}}
          <header class="flex w-full gap-8 p-2 border rounded-md border-neutral-200">
            {{-- proveedor --}}
            <div class="flex flex-col gap-1">
              <span class="font-semibold uppercase text-md text-neutral-500">de:</span>
              <span>
                <span class="font-semibold">Proveedor:</span>
                <span>{{ $quotation->supplier->company_name }}</span>
              </span>
              <span>
                <span class="font-semibold">CUIT:</span>
                <span>{{ $quotation->supplier->company_cuit }}</span>
              </span>
              <span>
                <span class="font-semibold">Teléfono:</span>
                <span>{{ $quotation->supplier->phone_number }}</span>
              </span>
              <span>
                <span class="font-semibold">Correo:</span>
                <span>{{ $quotation->supplier->user->email }}</span>
              </span>
              <span>
                <span class="font-semibold">Dirección:</span>
                <span>{{ $quotation->supplier->full_address }}</span>
              </span>
            </div>
            {{-- panaderia --}}
            <div class="flex flex-col gap-1">
              <span class="font-semibold uppercase text-md text-neutral-500">para:</span>
              <span>
                <span class="font-semibold">Panadería:</span>
                <span>{{ $razon_social }}</span>
              </span>
              <span>
                <span class="font-semibold">CUIT:</span>
                <span>{{ $cuit }}</span>
              </span>
              <span>
                <span class="font-semibold">Inicio de actividades:</span>
                <span>{{ $inicio_actividades }}</span>
              </span>
              <span>
                <span class="font-semibold">Teléfono:</span>
                <span>{{ $telefono }}</span>
              </span>
              <span>
                <span class="font-semibold">Correo:</span>
                <span>{{ $correo }}</span>
              </span>
              <span>
                <span class="font-semibold">Dirección:</span>
                <span>{{ $direccion }}</span>
              </span>
            </div>
          </header>
          {{-- contenido --}}
          <x-div-toggle x-data="{ open: true }" class="w-full p-2">
  
            <x-slot:title>
              <span class="my-2 text-xl md:text-base lg:text-sm">productos del presupuesto</span>
            </x-slot:title>
  
            <x-slot:subtitle>
              <span class="my-2 text-xl md:text-base lg:text-sm">modifique los siguientes precios y disponibilidad para cada producto:</span>
            </x-slot:subtitle>
  
            <x-slot:messages>
              @if ($errors->any())
                <span class="text-sm font-semibold text-red-400">¡hay errores en esta sección!</span>
              @endif
            </x-slot:messages>

            @if ($errors->any())
            <div class="mb-2">
              <ul class="list-disc list-inside">
                @error('inputs.*.item_unit_price*')
                <li class="text-red-400">{{ $message }}</li>
                @enderror
                @error('inputs.*.item_total_price*')
                <li class="text-red-400">{{ $message }}</li>
                @enderror
              </ul>
            </div>
            @endif
  
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th class="text-end">
                    #
                  </x-table-th>
                  <x-table-th class="text-start">
                    suministro/pack
                  </x-table-th>
                  <x-table-th class="text-start">
                    marca/volumen
                    <x-quest-icon title="kilogramos (K), gramos (g), litros (L), mililitros (mL), metros (M), centimetros (cm), unidades (U)" />
                  </x-table-th>
                  <x-table-th class="text-end">
                    presupuestar
                    <x-quest-icon title="cantidad que la panaderia desea presupuestar" />
                  </x-table-th>
                  <x-table-th class="text-end">
                    tiene stock?
                    <x-quest-icon title="¿actualmente tiene en stock la cantidad solicitada?" />
                  </x-table-th>
                  <x-table-th class="text-end">
                    $precio unitario
                    <x-quest-icon title="precio unitario de un suministro o pack, ejemplo: 1230.20 (mil doscientos treinta con veinte)" />
                    <span class="text-red-400">*</span>
                  </x-table-th>
                  <x-table-th class="text-end">
                    $subtotal
                    <x-quest-icon
                      title="precio subtotal del suministro o pack para la cantidad presupuestada, ejemplo: 1230.20 (mil doscientos treinta con veinte)" />
                    <span class="text-red-400">*</span>
                  </x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                @forelse ($inputs as $key => $input)
                <tr class="border">
                  <x-table-td class="text-end">
                    {{ $key + 1 }}
                  </x-table-td>
                  @if ($input['item_type'] === 'suministro')
                    {{-- mostrar suministro --}}
                    <x-table-td class="text-start">
                      {{ $input['item_object']->provision_name }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $input['item_object']->trademark->provision_trademark_name }}
                      {{ convert_measure($input['item_object']->provision_quantity, $input['item_object']->measure) }}
                    </x-table-td>
                    <x-table-td class="text-end">
                      {{ $input['item_quantity'] }}
                    </x-table-td>
                    {{-- stock --}}
                    <x-table-td class="text-end">
                      <div class="flex items-center justify-end w-full gap-1">
                        <input
                          type="checkbox"
                          id="input_{{ $key }}_item_has_stock"
                          wire:model.live="inputs.{{ $key }}.item_has_stock"
                          @checked(true)
                          class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                        />
                        @if ($inputs[$key]['item_has_stock'])
                          <span>si</span>
                        @else
                          <span>no</span>
                        @endif
                      </div>
                    </x-table-td>
                    {{-- precio unitario --}}
                    <x-table-td class="text-end">
                      <div class="flex flex-col w-full gap-1">
                        <div class="flex items-center justify-start w-full gap-1">
                          <span>$&nbsp;</span>
                          <input
                            type="text"
                            id="input_{{ $key }}_item_unit_price"
                            wire:model.live="inputs.{{ $key }}.item_unit_price"
                            wire:change="calculateSubtotal({{ $key }}, $event.target.value)"
                            pattern="[0-9.,]*"
                            class="p-1 w-32 text-sm text-right border grow border-neutral-200 @error('inputs.' . $key . '.item_unit_price') border-red-200 @enderror focus:outline-none focus:ring focus:ring-neutral-300"
                            placeholder="precio unitario"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </x-table-td>
                    {{-- precio subtotal --}}
                    <x-table-td class="text-end">
                      <div class="flex flex-col w-full gap-1">
                        <div class="flex items-center justify-start w-full gap-1">
                          <span>$&nbsp;</span>
                          <input
                            type="text"
                            id="input_{{ $key }}_item_total_price"
                            wire:model.live="inputs.{{ $key }}.item_total_price"
                            wire:change="formatSubtotal({{ $key }}, $event.target.value)"
                            pattern="[0-9.,]*"
                            class="p-1 w-32 text-sm text-right border grow border-neutral-200 @error('inputs.' . $key . '.item_total_price') border-red-200 @enderror focus:outline-none focus:ring focus:ring-neutral-300"
                            placeholder="precio total"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </x-table-td>
                  @else
                    {{-- mostrar pack --}}
                    <x-table-td class="text-start">
                      {{ $input['item_object']->pack_name }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $input['item_object']->provision->trademark->provision_trademark_name }}
                      {{ convert_measure($input['item_object']->pack_quantity, $input['item_object']->provision->measure) }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $input['item_quantity'] }}
                    </x-table-td>
                    {{-- stock --}}
                    <x-table-td class="text-end">
                      <div class="flex items-center justify-end w-full gap-1">
                        <input
                          type="checkbox"
                          id="input_{{ $key }}_item_has_stock"
                          @checked(true)
                          wire:model.defer="inputs.{{ $key }}.item_has_stock"
                          class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                        />
                        @if ($inputs[$key]['item_has_stock'])
                          <span>si</span>
                        @else
                          <span>no</span>
                        @endif
                      </div>
                    </x-table-td>
                    {{-- precio unitario --}}
                    <x-table-td class="text-end">
                      <div class="flex flex-col w-full gap-1">
                        <div class="flex items-center justify-start w-full gap-1">
                          <span>$&nbsp;</span>
                          <input
                            type="text"
                            id="input_{{ $key }}_item_unit_price"
                            wire:model.live="inputs.{{ $key }}.item_unit_price"
                            wire:change="calculateSubtotal({{ $key }}, $event.target.value)"
                            pattern="[0-9.,]*"
                            class="p-1 w-32 text-sm text-right border grow border-neutral-200 @error('inputs.' . $key . '.item_unit_price') border-red-200 @enderror focus:outline-none focus:ring focus:ring-neutral-300"
                            placeholder="precio unitario"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </x-table-td>
                    {{-- precio subtotal --}}
                    <x-table-td class="text-end">
                      <div class="flex flex-col w-full gap-1">
                        <div class="flex items-center justify-start w-full gap-1">
                          <span>$&nbsp;</span>
                          <input
                            type="text"
                            id="input_{{ $key }}_item_total_price"
                            wire:model.defer="inputs.{{ $key }}.item_total_price"
                            wire:change="formatSubtotal({{ $key }}, $event.target.value)"
                            pattern="[0-9.,]*"
                            class="p-1 w-32 text-sm text-right border grow border-neutral-200 @error('inputs.' . $key . '.item_total_price') border-red-200 @enderror focus:outline-none focus:ring focus:ring-neutral-300"
                            placeholder="precio total"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </x-table-td>
                  @endif
                  @if ($loop->last)
                    <tr class="border">
                      <x-table-td colspan="6" class="font-semibold capitalize text-end">total</x-table-td>
                      <x-table-td class="font-semibold text-end">${{ toMoneyFormat($total) }}</x-table-td>
                    </tr>
                  @endif 
                </tr>
                @empty
                <tr class="border">
                  <td colspan="6">sin registros!</td>
                </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
  
  
          </x-div-toggle>
        </section>
      </x-slot:content>

      <x-slot:footer class="my-2">
        <div class="justify-end hidden w-full gap-2 mt-2 lg:flex">
          <x-a-button
            wire:navigate
            href="{{ route('quotations-quotations-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>
          <x-btn-button
            type="button"
            wire:click="submit"
            >modificar
          </x-btn-button>
        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
