<div>
  {{-- componente listar pedidos de presupuesto del proveedor en sesion --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de pedidos de presupuesto recibidos:"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden">
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content class="flex-col gap-1">

        {{-- texto descriptivo --}}
        <p>La siguiente es una lista de pedidos de presupuesto que recibió de parte de la panadería <i>nombre</i> </p>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">codigo del presupuesto</x-table-th>
              <x-table-th class="text-start">estado del presupuesto</x-table-th>
              <x-table-th class="text-start">periodo</x-table-th>
              <x-table-th class="text-start">disponible hasta</x-table-th>
              <x-table-th class="text-start w-60">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($quotations as $quotation)
              <tr wire:key="{{ $quotation->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $quotation->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $quotation->quotation_code }}
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
                  <span>
                    {{ $quotation->period->period_code }}
                  </span>
                  <span>,&nbsp;el periodo está:&nbsp;</span>
                  {{-- estado del periodo --}}
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

                </x-table-td>
                <x-table-td class="text-start">
                  {{ formatDateTime($quotation->period->period_end_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">

                    {{-- no puedo responder si esta cerrado el periodo --}}
                    @if ($quotation->period->period_status_id !== 3)
                    <x-a-button
                      wire:navigate
                      href="{{ route('quotations-quotations-respond', $quotation->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >responder</x-a-button>
                    @endif

                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="8">¡sin registros! - aún no ha recibido pedidos de presupuesto.</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $quotations->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
