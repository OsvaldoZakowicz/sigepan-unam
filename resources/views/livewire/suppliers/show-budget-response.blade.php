<div>
  {{-- componente ver respuestas de un presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section>

      <x-slot:title>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">ver presupuesto,&nbsp;</span>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">código:&nbsp;</span>
        <span class="text-sm font-semibold uppercase">{{ $quotation->quotation_code }}.</span>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">fecha de presupuesto:&nbsp;</span>
        <span class="font-semibold">{{ formatDateTime($quotation->updated_at, 'd-m-Y H:i:s') }} (último cambio).</span>
      </x-slot:title>

      <x-a-button wire:navigate href="{{ route('suppliers-budgets-periods-show', $quotation->period->id) }}"
        bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">volver al periodo
      </x-a-button>

      {{-- todo: boton imprimir para obtener este presupuesto en pdf --}}

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

      <x-slot:content class="flex-col overflow-x-auto overflow-y-auto">
        {{-- un presupuesto --}}
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
          <x-div-toggle x-data="{ open: true }" class="p-2 mt-2" title="suministros y packs de este presupuesto">
            <div class="w-full overflow-x-auto overflow-y-auto max-h-72">
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-100">
                    <x-table-th class="text-end">
                      #
                    </x-table-th>
                    <x-table-th class="text-start">
                      Suministro/Pack
                    </x-table-th>
                    <x-table-th class="text-start">
                      Marca/Tipo/volumen
                    </x-table-th>
                    <x-table-th class="text-end">
                      Cantidad presupuestada
                    </x-table-th>
                    <x-table-th class="text-start">
                      Tiene stock
                    </x-table-th>
                    <x-table-th class="text-end">
                      $Precio unitario
                    </x-table-th>
                    <x-table-th class="text-end">
                      $Subtotal
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @foreach ($provisions_packs as $key => $item)
                  <tr class="border">
                    <x-table-td class="text-end">
                      {{ $key+1 }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $item['name'] }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $item['description'] }}
                    </x-table-td>
                    <x-table-td class="text-end">
                      {{ $item['quantity'] }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      @if ($quotation->is_completed)
                      @if ($item['has_stock'])
                      <x-text-tag color="emerald">si</x-text-tag>
                      @else
                      <x-text-tag color="red">no</x-text-tag>
                      @endif
                      @else
                      <span>sin respuesta</span>
                      @endif
                    </x-table-td>
                    <x-table-td class="text-end">
                      @if ($quotation->is_completed)
                      ${{ toMoneyFormat($item['unit_price']) }}
                      @else
                      <span>sin respuesta</span>
                      @endif
                    </x-table-td>
                    <x-table-td class="text-end">
                      @if ($quotation->is_completed)
                      ${{ toMoneyFormat($item['total_price']) }}
                      @else
                      <span>sin respuesta</span>
                      @endif
                    </x-table-td>
                  </tr>
                  @if ($loop->last)
                  <tr class="border">
                    <x-table-td colspan="6" class="font-semibold capitalize text-end">$total:</x-table-td>
                    <x-table-td class="font-semibold text-end">${{ toMoneyFormat($total) }}</x-table-td>
                  </tr>
                  @endif
                  @endforeach
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