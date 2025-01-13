<div>
    {{-- componente responder a una solicitud de presupuestos --}}
    {{-- componente crear proveedor --}}
    <article class="m-2 border rounded-sm border-neutral-200">

        {{-- barra de titulo --}}
        <x-title-section title="responder peticion de presupuesto"></x-title-section>

        {{-- cuerpo --}}
        <x-content-section>

          <x-slot:header class="hidden"></x-slot:header>

          {{-- todo: agregar altura maxima y scroll en el eje y --}}
          <x-slot:content class="flex-col">

            {{-- todo: cabecera desplegable con datos del emisor, receptor, presupuesto, periodo y fecha de ultima modificación --}}

            {{-- formulario --}}
            <x-fieldset-base tema="{{ $quotation->quotation_code }}" class="w-full p-2">


              {{-- leyenda --}}
              {{-- todo: mejorar texto descriptivo de leyenda --}}
              <span>complete los siguientes precios:</span>

              {{-- todo: mensajes de error aqui, uno por tipo de error --}}
              {{-- todo: marcar en rojo los inputs con error --}}

              {{-- inputs --}}
              {{-- todo: cambiar formato de tabla e incluir columnas extra segun diseño --}}
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-100">
                    <x-table-th class="text-end w-12">id</x-table-th>
                    <x-table-th class="text-start">suministro</x-table-th>
                    <x-table-th class="text-end w-1/3">$&nbsp;precio unitario<span class="text-red-400">*</span></x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @forelse ($inputs as $key => $input)
                  <tr class="border">
                    <x-table-td class="text-end">
                      {{ $input['provision_id'] }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      <span class="font-semibold">producto</span>:&nbsp;
                      {{ $input['provision_name'] }},&nbsp;
                      <span class="font-semibold">marca</span>:&nbsp;
                      {{ $input['provision_trademark'] }},&nbsp;
                      {{ $input['provision_quantity'] }}&nbsp;({{ $input['provision_quantity_abrv'] }}),&nbsp;
                      <span class="font-semibold">cantidad: 1</span>
                    </x-table-td>
                    <x-table-td>
                      {{-- precio --}}
                      <div class="flex flex-col gap-1 w-full">
                        @error('inputs.' . $key . '.price') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        <div class="flex w-full gap-1 items-center justify-start">
                          <span>$&nbsp;</span>
                          <input type="text" id="input_{{ $key }}_price" wire:model.defer="inputs.{{ $key }}.price" class="grow text-right p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" placeholder="precio unitario" autocomplete="off" />
                        </div>
                      </div>
                    </x-table-td>
                  </tr>
                  @empty
                  <tr class="border">
                    <td colspan="3">sin registros!</td>
                  </tr>
                  @endforelse
                </x-slot:tablebody>
              </x-table-base>

            </x-fieldset-base>

          </x-slot:content>

          <x-slot:footer class="my-2">
            {{-- boton de guardado --}}
            <div class="w-full flex justify-end gap-2 mt-2">
              <x-a-button wire:navigate href="{{ route('quotations-quotations-index') }}" bg_color="neutral-600" border_color="neutral-600">cancelar</x-a-button>

              <x-btn-button type="button" wire:click="submit">presupuestar</x-btn-button>
            </div>
          </x-slot:footer>

        </x-content-section>

    </article>
</div>
