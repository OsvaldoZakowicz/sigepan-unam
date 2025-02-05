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

      <x-slot:header class="hidden">
      </x-slot:header>

      <x-slot:content class="flex-col overflow-y-auto">

        {{-- cabecera del presupuesto--}}
        <x-div-toggle x-data="{ open: false }" class="w-full p-2">

          <x-slot:title>
            <span class="text-xl md:text-base lg:text-sm my-2">datos del presupuesto</span>
          </x-slot:title>

          <x-slot:subtitle>
            <span class="text-xl md:text-base lg:text-sm my-2" >datos del emisor, receptor, periodo y presupuesto:</span>
          </x-slot:subtitle>

          <div class="flex flex-wrap gap-2 text-xl md:text-base lg:text-sm text-neutral-700">

            <div class="w-full flex flex-col gap-1">
              {{-- periodo --}}
              <div class="flex flex-col border border-neutral-300 p-2 rounded-md">
                {{-- renglon de titulo de seccion --}}
                <span role="renglon" class="mb-2 text-xl md:text-base text-neutral-800">
                  <span class="font-bold">Período:</span>
                </span>
                {{-- renglon codigo de periodo --}}
                <span role="renglon">
                  <span class="font-semibold">Código del periodo:&nbsp;</span>
                  <span class="italic">{{ $quotation->period->period_code }}</span>
                </span>
                {{-- renglon estado del periodo --}}
                <span role="renglon">
                  <span class="font-semibold">Estado del periodo:&nbsp;</span>
                  <span>el periodo está:&nbsp;</span>
                  <span>
                    @switch($quotation->period->status->status_code)

                      @case(1)
                        {{-- abierto --}}
                        <span
                          class="font-semibold text-emerald-600 cursor-pointer"
                          title="{{ $quotation->period->status->status_short_description }}"
                          >{{ $quotation->period->status->status_name }}
                        </span>
                        @break

                      @default
                        {{-- cerrado --}}
                        <span
                          class="font-semibold text-red-400 cursor-pointer"
                          title="{{ $quotation->period->status->status_short_description }}"
                          >{{ $quotation->period->status->status_name }}
                        </span>

                    @endswitch
                  </span>
                </span>
                {{-- renglon fecha de apertura --}}
                <span role="renglon">
                  <span class="font-semibold">Fecha de apertura:&nbsp;</span>
                  <span class="italic">{{ formatDateTime($quotation->period->period_start_at, 'd-m-Y H:i:s') }}&nbsp;hs</span>
                </span>
                {{-- renglon fecha de cierre --}}
                <span role="renglon">
                  <span class="font-semibold">Fecha de cierre:&nbsp;</span>
                  <span class="italic">{{ formatDateTime($quotation->period->period_end_at, 'd-m-Y H:i:s') }}&nbsp;hs</span>
                </span>
              </div>

              {{-- datos del presupuesto --}}
              <div class="flex flex-col border border-neutral-300 p-2 rounded-md">
                {{-- renglon de titulo de seccion --}}
                <span role="renglon" class="mb-2 text-xl md:text-base text-neutral-800">
                  <span class="font-semibold">Presupuesto:</span>
                </span>
                {{-- renglon codigo del presupuesto --}}
                <span role="renglon">
                  <span class="font-semibold">Código del presupuesto:&nbsp;</span>
                  <span class="italic">{{ $quotation->quotation_code }}</span>
                </span>
                {{-- fecha de ultima respuesta --}}
                <span role="renglon">
                  <span class="font-semibold">Última respuesta:&nbsp;</span>
                  @if ($quotation->is_completed)
                    <span class="font-semibold text-emerald-600">{{ $quotation->updated_at }}</span>
                  @else
                    <span class="font-semibold text-red-400">sin responder</span>
                  @endif
                </span>
              </div>
            </div>

            <div class="w-full flex flex-col gap-1">
              {{-- datos del emisor: sera el provedor que esta respondiendo a este presupuesto --}}
              <div class="flex flex-col border border-neutral-300 p-2 rounded-md">
                {{-- renglon de titulo de seccion --}}
                <span role="renglon" class="mb-2 text-xl md:text-base text-neutral-800">
                  <span class="font-semibold">Entidad emisora:</span>
                </span>
                {{-- renglon de proveedor --}}
                <span role="renglon">
                  <span class="font-semibold">Proveedor:&nbsp;</span>
                  <span class="italic">{{ $quotation->supplier->company_name }}</span>
                </span>
                {{-- renglon de CUIT --}}
                <span role="renglon">
                  <span class="font-semibold">CUIT:&nbsp;</span>
                  <span class="italic">{{ $quotation->supplier->company_cuit }}</span>
                </span>
                {{-- renglon de condicion del iva --}}
                <span role="renglon">
                  <span class="font-semibold">Condición de IVA:&nbsp;</span>
                  <span class="italic">{{ $quotation->supplier->iva_condition }}</span>
                </span>
                {{-- renglon de telefono --}}
                <span role="renglon">
                  <span class="font-semibold">Teléfono:&nbsp;</span>
                  <span class="italic">{{ $quotation->supplier->phone_number}}</span>
                </span>
                {{-- renglon de correo --}}
                <span role="renglon">
                  <span class="font-semibold">Correo:&nbsp;</span>
                  <span class="italic">{{ $quotation->supplier->user->email }}</span>
                </span>
                {{-- renglon de direccion --}}
                <span role="renglon">
                  <span class="font-semibold">Dirección:&nbsp;</span>
                  <span class="italic">calle:&nbsp;
                    {{ $quotation->supplier->address->street }}&nbsp;
                    {{ $quotation->supplier->address->number }},&nbsp;
                    {{ $quotation->supplier->address->postal_code }}&nbsp;
                    {{ $quotation->supplier->address->city }}&nbsp;
                  </span>
                </span>
              </div>

              {{-- datos del receptor: sera la panaderia que obtendra este presupuesto --}}
              <div class="flex flex-col border border-neutral-300 p-2 rounded-md">
                {{-- renglon de titulo de seccion --}}
                <span role="renglon" class="mb-2 text-xl md:text-base text-neutral-800">
                  <span class="font-semibold">Entidad receptora:</span>
                </span>
                {{-- renglon de panaderia --}}
                <span role="renglon">
                  <span class="font-semibold">Panaderia:&nbsp;</span>
                  <span class="italic"></span>
                </span>
                {{-- renglon de CUIT --}}
                <span role="renglon">
                  <span class="font-semibold">CUIT:&nbsp;</span>
                  <span class="italic"></span>
                </span>
                {{-- renglon de condicion del iva --}}
                <span role="renglon">
                  <span class="font-semibold">Condición de IVA:&nbsp;</span>
                  <span class="italic"></span>
                </span>
                {{-- renglon de telefono --}}
                <span role="renglon">
                  <span class="font-semibold">Teléfono:&nbsp;</span>
                  <span class="italic"></span>
                </span>
                {{-- renglon de correo --}}
                <span role="renglon">
                  <span class="font-semibold">Correo:&nbsp;</span>
                  <span class="italic"></span>
                </span>
                {{-- renglon de direccion --}}
                <span role="renglon">
                  <span class="font-semibold">Dirección:&nbsp;</span>
                  <span class="italic">calle:&nbsp;
                  </span>
                </span>
              </div>
            </div>

          </div>


        </x-div-toggle>

        {{-- cuerpo del presupuesto --}}
        <x-div-toggle x-data="{ open: true }" class="w-full p-2">

          <x-slot:title>
            <span class="text-xl md:text-base lg:text-sm my-2">productos del presupuesto</span>
          </x-slot:title>

          <x-slot:subtitle>
            <span class="text-xl md:text-base lg:text-sm my-2">detalle del presupuesto:</span>
          </x-slot:subtitle>


          {{-- todo: limitar la altura de la tabla --}}
          {{-- tabla de datos: mobile first, display hasta 1023px de ancho --}}
          <div class="lg:hidden overflow-y-auto overflow-x-hidden">
            {{-- listado de productos --}}
            {{-- <x-table-base class="lg:hidden">
              <x-slot:tablehead>
                <tr class="border bg-neutral-100 p-2">
                  <x-table-th colspan="2" class="text-start text-xl md:text-base">lista de productos:</x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>

                @foreach ($inputs as $key => $input)


                  <tr class="borde text-xl md:text-base lg:text-sm bg-neutral-100">
                    <x-table-td>renglon:&nbsp;#{{ $key+1 }}</x-table-td>
                    <x-table-td class="text-end">
                      @php
                        $input_error = 'inputs.' . $key . '.price';
                      @endphp
                      @error($input_error)
                        <span class="text-red-400 font semibold text-sm">{{ $message }}</span>
                      @enderror
                    </x-table-td>
                  </tr>


                  <tr class="border text-xl md:text-base lg:text-sm">
                    <x-table-td>nombre:</x-table-td>
                    <x-table-td class="text-end">
                      {{ $input['provision_name'] }}</x-table-td>
                  </tr>
                  <tr class="border text-xl md:text-base lg:text-sm">
                    <x-table-td>marca:</x-table-td>
                    <x-table-td class="text-end">
                      {{ $input['provision_trademark'] }}
                    </x-table-td>
                  </tr>
                  <tr class="border text-xl md:text-base lg:text-sm">
                    <x-table-td>volumen:</x-table-td>
                    <x-table-td class="text-end">
                      {{ $input['provision_quantity'] }},&nbsp;{{ $input['provision_quantity_abrv'] }}
                    </x-table-td>
                  </tr>

                  <tr class="border text-xl md:text-base lg:text-sm">
                    <x-table-td>cantidad:</x-table-td>
                    <x-table-td class="text-end">unidad/pack</x-table-td>
                  </tr>
                  <tr class="border text-xl md:text-base lg:text-sm">
                    <x-table-td>tiene stock?</x-table-td>
                    <x-table-td class="text-end">
                      <input
                        type="checkbox"
                        id="input_{{ $key }}_has_stock"
                        @checked(true)
                        wire:model.defer="inputs.{{ $key }}.has_stock"
                        class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                    </x-table-td>
                  </tr>
                  <tr class="border border-b-2 border-neutral-400 text-xl md:text-base lg:text-sm">
                    <x-table-td>precio:</x-table-td>
                    <x-table-td>
                      <div class="flex w-full gap-1 items-center justify-start">
                        <span class="text-red-400">*</span>
                        <span>$&nbsp;</span>
                        @php
                          $input_error = 'inputs.' . $key . '.price';
                        @endphp
                        <input
                          type="text"
                          id="input_{{ $key }}_price"
                          name="inputs.{{ $key }}.price"
                          wire:model.defer="inputs.{{ $key }}.price"
                          class="grow text-right p-1 text-lg md:text-base border  focus:outline-none focus:ring focus:ring-neutral-300 @error($input_error) border-red-400 @else border-neutral-200 @enderror"
                          placeholder="precio"
                          autocomplete="off"
                        />
                      </div>
                    </x-table-td>
                  </tr>

                @endforeach

              </x-slot:tablebody>
            </x-table-base> --}}
          </div>


          {{-- tabla d datos: desktop, display desde 1024px --}}
          <x-table-base class="hidden lg:table">
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="text-end w-12">renglón:</x-table-th>
                <x-table-th class="text-start">nombre</x-table-th>
                <x-table-th class="text-start">marca</x-table-th>
                <x-table-th class="text-end">volumen</x-table-th>
                <x-table-th class="text-start">cantidad a presupuestar</x-table-th>
                <x-table-th class="text-end">tiene stock?</x-table-th>
                <x-table-th class="text-end">$&nbsp;precio unitario<span class="text-red-400">*</span></x-table-th>
                <x-table-th class="text-end">$&nbsp;precio total<span class="text-red-400">*</span></x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              @forelse ($rows as $key => $row)
              <tr class="border">
                <x-table-td class="text-end">
                  #{{ $key+1 }}
                </x-table-td>
                @if ($row['item_type'] === 'suministro')
                  {{-- mostrar suministro --}}
                  <x-table-td class="text-start">
                    {{ $row['item_object']->provision_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $row['item_object']->trademark->provision_trademark_name }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    {{ $row['item_object']->provision_quantity }}&nbsp;({{ $row['item_object']->measure->measure_abrv }}),&nbsp;
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $row['item_quantity'] }}
                  </x-table-td>
                  {{-- stock --}}
                  <x-table-td class="text-end">
                    @if ($row['item_has_stock'])<span>si</span>@else<span>no</span>@endif
                  </x-table-td>
                  {{-- precio unitario --}}
                  <x-table-td class="text-end">
                    @if ($row['item_has_stock'])$&nbsp;{{ $row['item_unit_price'] }}@else<span>-</span>@endif
                  </x-table-td>
                  {{-- precio total --}}
                  <x-table-td class="text-end">
                    @if ($row['item_has_stock'])$&nbsp;{{ $row['item_total_price'] }}@else<span>-</span>@endif
                  </x-table-td>
                @else
                  {{-- mostrar pack --}}
                  <x-table-td class="text-start">
                    {{ $row['item_object']->pack_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $row['item_object']->provision->trademark->provision_trademark_name }}
                  </x-table-td>
                  <x-table-td class="text-end">
                    {{ $row['item_object']->pack_quantity }}&nbsp;({{ $row['item_object']->provision->measure->measure_abrv }}),&nbsp;
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $row['item_quantity'] }}
                  </x-table-td>
                  {{-- stock --}}
                  <x-table-td class="text-end">
                    @if ($row['item_has_stock'])<span>si</span>@else<span>no</span>@endif
                  </x-table-td>
                  {{-- precio unitario --}}
                  <x-table-td class="text-end">
                    @if ($row['item_has_stock'])$&nbsp;{{ $row['item_unit_price'] }}@else<span>-</span>@endif
                  </x-table-td>
                  {{-- precio total --}}
                  <x-table-td class="text-end">
                    @if ($row['item_has_stock'])$&nbsp;{{ $row['item_total_price'] }}@else<span>-</span>@endif
                  </x-table-td>
                @endif

              </tr>
              @empty
              <tr class="border">
                <td colspan="7">sin registros!</td>
              </tr>
              @endforelse
            </x-slot:tablebody>
          </x-table-base>

        </x-div-toggle>

      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
