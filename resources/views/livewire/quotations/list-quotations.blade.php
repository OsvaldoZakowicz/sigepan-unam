<div>
  {{-- componente listar pedidos de presupuesto del proveedor en sesion --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de pedidos de presupuesto recibidos:"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_word">buscar presupuesto</label>
            <input
              type="text"
              name="search_word"
              id="search_word"
              wire:model.live="search_word"
              wire:click="resetPagination()"
              placeholder="ingrese un id, codigo de presupuesto o de periodo ..."
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- estado del presupuesto --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="quotation_status">estado del presupuesto</label>
            <select
              name="quotation_status"
              id="quotation_status"
              wire:model.live="quotation_status"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione un estado ...</option>
              <option value="1">respondido</option>
              <option value="0">sin responder</option>
            </select>
          </div>

          {{-- estado del periodo --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="period_status">estado del periodo</label>
            <select
              name="period_status"
              id="period_status"
              wire:model.live="period_status"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione un estado ...</option>
              <option value="2">abierto</option>
              <option value="3">cerrado</option>
            </select>
          </div>

          {{-- fecha de inicio --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="period_start_at">fecha de recepción desde</label>
            <input
              type="date"
              name="period_start_at"
              id="period_start_at"
              wire:model.live="period_start_at"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- fecha de fin --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="period_end_at">fecha de disponibilidad hasta</label>
            <input
              type="date"
              name="period_end_at"
              id="period_end_at"
              wire:model.live="period_end_at"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

        </div>

        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>

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
              <x-table-th class="text-start">recibido el</x-table-th>
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
                  <span>{{ $quotation->period->period_code }},&nbsp;el periodo está:&nbsp;</span>
                  {{-- estado del periodo --}}
                  @if ($quotation->period->status->status_code == 1)
                    {{-- abierto --}}
                    <span
                      class="font-semibold text-emerald-600 cursor-pointer"
                      title="{{ $quotation->period->status->status_short_description }}"
                      >{{ $quotation->period->status->status_name }}
                    </span>
                  @else
                    {{-- cerrado --}}
                    <span
                      class="font-semibold text-red-400 cursor-pointer"
                      title="{{ $quotation->period->status->status_short_description }}"
                      >{{ $quotation->period->status->status_name }}
                    </span>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  {{ formatDateTime($quotation->period->period_start_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ formatDateTime($quotation->period->period_end_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">
                    {{-- si el periodo NO esta cerrado --}}
                    @if ($quotation->period->period_status_id !== 3)

                      {{-- responder si no esta completado, de lo contrario, editar --}}
                      @if ($quotation->is_completed)

                        <x-a-button
                          wire:navigate
                          href="#"
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >modificar
                        </x-a-button>

                      @else

                        <x-a-button
                          wire:navigate
                          href="{{ route('quotations-quotations-respond', $quotation->id) }}"
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >responder
                        </x-a-button>

                      @endif

                    @else

                      <x-a-button
                        wire:navigate
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >ver
                      </x-a-button>

                    @endif
                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="7">¡sin registros! - aún no ha recibido pedidos de presupuesto.</td>
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
