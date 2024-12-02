<div>
  {{-- componente ver periodo de peticion de presupuestos --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver periodo presupuestario: {{ $period->period_code }}"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">
        <div class="w-full flex justify-start items-center gap-2">
          <span>
            <span class="font-semibold">Estado actual:</span>&nbsp;
            {{-- manejar tres estados distintos --}}
            @switch($period->status->status_code)
              @case(0)
                {{-- programado --}}
                <span
                  class="font-semibold text-neutral-600 cursor-pointer"
                  title="{{ $period->status->status_short_description }}"
                  >{{ $period->status->status_name }}
                </span>
                {{-- mostrar cuanto falta para iniciar --}}
                {{-- todo: corregir calculo --}}
                {{-- <span>inicia en:</span>
                <span>{{ diffInDays(null, $period->period_start_at) }}</span>
                <span>días.</span> --}}
                @break

              @case(1)
                {{-- abierto --}}
                <span
                  class="font-semibold text-emerald-600 cursor-pointer"
                  title="{{ $period->status->status_short_description }}"
                  >{{ $period->status->status_name }}
                </span>
                {{-- mostrar cuanto falta para cerrar --}}
                {{-- <span>cierra en:</span>
                <span>{{ diffInDays(null, $period->period_end_at) }}</span>
                <span>días.</span> --}}
                @break

              @default
                {{-- cerrado --}}
                <span
                  class="font-semibold text-red-400 cursor-pointer"
                  title="{{ $period->status->status_short_description }}"
                  >{{ $period->status->status_name }}
                </span>
            @endswitch
          </span>
          <span><span class="font-semibold">Apertura:</span>&nbsp;{{ formatDateTime($period->period_start_at, 'd-m-Y') }}</span>
          <span><span class="font-semibold">Cierre:</span>&nbsp;{{ formatDateTime($period->period_end_at, 'd-m-Y') }}</span>
        </div>
        <div class="w-1/4 flex justify-end items-center gap-2">
          <x-a-button wire:navigate href="#" bg_color="red-600" border_color="red-600" text_color="neutral-100">cerrar ahora</x-a-button>
        </div>
      </x-slot:header>

      <x-slot:content class="flex-col">

        <span class="font-semibold mt-2 mb-1">suministros de interés:</span>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">suministro</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($period_provisions as $provision)
              <tr wire:key="{{ $provision->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $provision->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  <span class="font-semibold">suministro</span>:&nbsp;
                  {{ $provision->provision_name }},&nbsp;
                  <span class="font-semibold">marca</span>:&nbsp;
                  {{ $provision->trademark->provision_trademark_name }},&nbsp;
                  {{ $provision->provision_quantity }}&nbsp;({{ $provision->measure->measure_abrv }}),&nbsp;
                  <span class="font-semibold">cantidad: 1</span>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="2">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        <span class="font-semibold mt-2 mb-1">presupuestos enviados:</span>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">presupuesto</x-table-th>
              <x-table-th class="text-start">proveedor</x-table-th>
              <x-table-th class="text-start">estado</x-table-th>
              <x-table-th class="text-end">fecha de recepción</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($period_quotations as $quotation)
              {{-- fila proveedor --}}
              <tr wire:key="{{ $quotation->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $quotation->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $quotation->quotation_code }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $quotation->supplier->company_name }},&nbsp;CUIT:&nbsp;{{ $quotation->supplier->company_cuit }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- estado del presupuesto --}}
                  @if ($quotation->is_completed)
                    <span class="font-semibold text-emerald-600">respondido</span>
                  @else
                    <span class="font-semibold text-red-400">sin responder</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  {{ formatDateTime($quotation->created_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td>

                  @if ($quotation->is_completed)
                    <x-a-button
                      wire:navigate
                      href="{{ route('suppliers-budgets-response', $quotation->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver</x-a-button>
                  @else
                    <p>-</p>
                  @endif

                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="2">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- todo: crear ranking de precios --}}

      </x-slot:content>

      <x-slot:footer class="my-2">
        <div></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
