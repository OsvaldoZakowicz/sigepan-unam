<div>
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver presupuesto">
      <x-a-button
        wire:navigate
        href="{{ route('quotations-quotations-index') }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver
      </x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- estado --}}
        <div class="flex items-center justify-between gap-2">
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

            <div class="overflow-x-auto overflow-y-auto max-h-72">
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
                    </x-table-th>
                    <x-table-th class="text-end">
                      presupuestar
                    </x-table-th>
                    <x-table-th class="text-end">
                      tiene stock?
                    </x-table-th>
                    <x-table-th class="text-end">
                      $precio unitario
                    </x-table-th>
                    <x-table-th class="text-end">
                      $subtotal
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @forelse ($rows as $key => $input)
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
                            @if ($rows[$key]['item_has_stock'])
                              <x-text-tag color="emerald">si</x-text-tag>
                            @else
                              <x-text-tag color="red">no</x-text-tag>
                            @endif
                          </div>
                        </x-table-td>
                        {{-- precio unitario --}}
                        <x-table-td class="text-end">
                          ${{ toMoneyFormat($input['item_unit_price']) }}
                        </x-table-td>
                        {{-- precio subtotal --}}
                        <x-table-td class="text-end">
                          ${{ toMoneyFormat($input['item_total_price']) }}
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
                        <x-table-td class="text-end">
                          {{ $input['item_quantity'] }}
                        </x-table-td>
                        {{-- stock --}}
                        <x-table-td class="text-end">
                          <div class="flex items-center justify-end w-full gap-1">
                            @if ($rows[$key]['item_has_stock'])
                              <x-text-tag color="emerald">si</x-text-tag>
                            @else
                              <x-text-tag color="red">no</x-text-tag>
                            @endif
                          </div>
                        </x-table-td>
                        {{-- precio unitario --}}
                        <x-table-td class="text-end">
                          ${{ toMoneyFormat($input['item_unit_price']) }}
                        </x-table-td>
                        {{-- precio subtotal --}}
                        <x-table-td class="text-end">
                          ${{ toMoneyFormat($input['item_total_price']) }}
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
                      <td colspan="7">sin registros!</td>
                    </tr>
                  @endforelse
                </x-slot:tablebody>
              </x-table-base>
            </div>
          </x-div-toggle>
        </section>
      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
