<div wire:poll>
  {{-- componente ver periodo de peticion de pre ordenes --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver periodo de pre orden: {{ $preorder_period->period_code }}">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-preorders-index') }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- titulo de encabezado --}}
        <div class="w-full flex justify-start items-center gap-2">
          <span>
            <span class="font-semibold">Estado actual:</span>&nbsp;
            {{-- estado del periodo --}}
            @switch($preorder_period->status->status_code)

              @case(0)
                {{-- programado --}}
                <span
                  class="font-semibold text-neutral-600 cursor-pointer"
                  title="{{ $preorder_period->status->status_short_description }}"
                  >{{ $preorder_period->status->status_name }}
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
                  title="{{ $preorder_period->status->status_short_description }}"
                  >{{ $preorder_period->status->status_name }}
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
                  title="{{ $preorder_period->status->status_short_description }}"
                  >{{ $preorder_period->status->status_name }}
                </span>

            @endswitch
          </span>
          <span>
            <span class="font-semibold">Apertura:</span>&nbsp;
            {{ formatDateTime($preorder_period->period_start_at, 'd-m-Y') }}
          </span>
          <span>
            <span class="font-semibold">Cierre:</span>&nbsp;
            {{ formatDateTime($preorder_period->period_end_at, 'd-m-Y') }}
          </span>
        </div>

        {{-- botones con acciones --}}
        <div class="w-2/3 flex justify-end items-center gap-2">

          @if ($preorder_period->period_status_id === $scheduled)
            <x-a-button
              href="#"
              wire:click="openPeriod()"
              bg_color="emerald-600"
              border_color="emerald-600"
              text_color="neutral-100"
              wire:confirm="¿Abrir este período?, se enviarán las pre ordenes a los proveedores activos de los suministros de interés"
              >abrir ahora
            </x-a-button>
          @elseif ($preorder_period->period_status_id === $opened)
            <x-a-button
              href="#"
              wire:click="closePeriod()"
              bg_color="red-600"
              border_color="red-600"
              text_color="neutral-100"
              wire:confirm="¿Cerrar este período?, los proveedores con pre ordenes sin responder quedarán fuera del periodo."
              >cerrar ahora
            </x-a-button>
          @endif

        </div>

      </x-slot:header>

      <x-slot:content class="flex-col">

        {{-- todo: suministros y packs de interes --}}

        {{-- pre ordenes --}}
        <x-div-toggle x-data="{ open: true }" title="pre ordenes solicitadas en este período:" class="p-2">

          <x-slot:subtitle>
            <span>lista de pre ordenes a la espera de ser respondidos en el período</span>
          </x-slot:subtitle>

          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="text-end w-12">id</x-table-th>
                <x-table-th class="text-start">pre orden</x-table-th>
                <x-table-th class="text-start">proveedor</x-table-th>
                <x-table-th class="text-start">estado</x-table-th>
                <x-table-th class="text-end">
                  <span>última respuesta</span>
                  <x-quest-icon title="última vez que el proveedor modificó los precios de este presupuesto"/>
                </x-table-th>
                <x-table-th class="text-start w-48">acciones</x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              @forelse ($preorders as $preorder)
                <tr wire:key="{{ $preorder->id }}" class="border">
                  <x-table-td class="text-end">
                    {{ $preorder->id }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $preorder->pre_order_code }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $preorder->supplier->company_name }},&nbsp;CUIT:&nbsp;{{ $preorder->supplier->company_cuit }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    @if ($preorder->is_completed)
                      <span class="font-semibold text-emerald-600">respondido</span>
                    @else
                      <span class="font-semibold text-red-400">sin responder</span>
                    @endif
                  </x-table-td>
                  <x-table-td class="text-end">
                    @if ($preorder->is_completed)
                      {{ formatDateTime($preorder->updated_at, 'd-m-Y') }}
                    @else
                      <span class="font-semibold text-red-400">-</span>
                    @endif
                  </x-table-td>
                  <x-table-td>

                    {{-- todo, respuesta del proveedor --}}
                    {{-- todo, vista parecida al preview? --}}
                    {{-- <x-a-button
                      wire:navigate
                      href="{{ route('suppliers-budgets-response', $quotation->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver
                    </x-a-button> --}}

                  </x-table-td>
                </tr>
              @empty
                <tr class="border">
                  <td colspan="6">¡sin registros hasta que el período comience!</td>
                </tr>
              @endforelse
            </x-slot:tablebody>
          </x-table-base>
          <div class="w-full flex justify-end items-center gap-1 mt-1">
            {{ $preorders->links() }}
          </div>
        </x-div-toggle>


      </x-slot:content>

      <x-slot:footer class="my-2">
        <div></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
