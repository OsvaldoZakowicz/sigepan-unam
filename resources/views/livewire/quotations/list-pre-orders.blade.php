<div>
  {{-- componente listar pedidos de preorden del proveedor en sesion --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de pre ordenes de compra recibidas:"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">
        {{-- busqueda --}}
        <div class="flex items-end justify-start gap-1">
          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-56">
            <label for="search_word">buscar pre orden</label>
            <input
              type="text"
              name="search_word"
              id="search_word"
              wire:model.live="search_word"
              wire:click="resetPagination()"
              placeholder="codigo de pre orden ..."
              class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- estado de la pre orden --}}
          <div class="flex flex-col justify-end w-48">
            <label for="preorder_status">estado</label>
            <select
              name="preorder_status"
              id="preorder_status"
              wire:model.live="preorder_status"
              wire:click="resetPagination()"
              class="w-full p-1 pr-8 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione un estado ...</option>
              <option value="1">respondido</option>
              <option value="0">sin responder</option>
            </select>
          </div>
          {{-- estado del periodo --}}
          <div class="flex flex-col justify-end w-48">
            <label for="period_status">periodo</label>
            <select
              name="period_status"
              id="period_status"
              wire:model.live="period_status"
              wire:click="resetPagination()"
              class="w-full p-1 pr-8 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione un estado ...</option>
              <option value="2">abierto</option>
              <option value="3">cerrado</option>
            </select>
          </div>
          {{-- fecha de inicio --}}
          <div class="flex flex-col justify-end w-48">
            <label for="period_start_at">fecha de recepción</label>
            <input
              type="date"
              name="period_start_at"
              id="period_start_at"
              wire:model.live="period_start_at"
              wire:click="resetPagination()"
              class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- fecha de fin --}}
          <div class="flex flex-col justify-end w-48">
            <label for="period_end_at">fecha de disponibilidad</label>
            <input
              type="date"
              name="period_end_at"
              id="period_end_at"
              wire:model.live="period_end_at"
              wire:click="resetPagination()"
              class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- limpiar campos de busqueda --}}
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar filtros
          </x-a-button>
        </div>
      </x-slot:header>

      <x-slot:content class="flex-col gap-1">

        {{-- texto descriptivo --}}
        <p class="my-2 font-semibold">La siguiente es una lista de pedidos de pre ordenes de compra que recibió de parte de la panadería <i>nombre</i> </p>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-12 text-end">
                id
              </x-table-th>
              <x-table-th class="text-start">
                codigo
              </x-table-th>
              <x-table-th class="text-start">
                estado
              </x-table-th>
              <x-table-th class="text-start">
                evaluación
              </x-table-th>
              <x-table-th class="text-start">
                periodo
              </x-table-th>
              <x-table-th class="text-start">
                recibido
              </x-table-th>
              <x-table-th class="text-start">
                disponible
              </x-table-th>
              <x-table-th class="text-start">
                orden
                <x-quest-icon 
                  title="disponible solo cuando el estado de la pre orden es aprobado"/>
              </x-table-th>
              <x-table-th class="w-24 text-start">
                acciones
              </x-table-th>
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
                  {{-- estado de la pre orden (respondido o no) --}}
                  @if ($preorder->is_completed)
                    <x-text-tag
                      color="emerald"
                      class="cursor-pointer"
                      >respondido
                      <x-quest-icon title="ya has respondido a esta pre orden"/>
                    </x-text-tag>
                  @else
                    <x-text-tag
                      color="red"
                      class="cursor-pointer"
                      >sin responder
                      <x-quest-icon title="no has respondido a esta pre orden"/>
                    </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- estado de la pre orden (pendiente, aprobado, rechazado) --}}
                  @if ($preorder->status === $status_pending)
                    <x-text-tag
                      color="neutral"
                      class="cursor-pointer"
                      >{{ $preorder->status }}
                      <x-quest-icon title="su respuesta será evaluada" />
                    </x-text-tag>
                  @elseif ($preorder->status === $status_approved)
                    <x-text-tag
                      color="emerald"
                      class="cursor-pointer"
                      >{{ $preorder->status }}
                      <x-quest-icon title="la panadería esta de acuerdo con la pre orden, y le enviará una orden de compra definitiva" />
                    </x-text-tag>
                  @else
                    <x-text-tag
                      color="red"
                      class="cursor-pointer"
                      >{{ $preorder->status }}
                      <x-quest-icon title="la panadería decidio no continuar con la orden de compra" />
                    </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  <span>{{ $preorder->pre_order_period->period_code }}&nbsp;</span>
                  {{-- estado del periodo --}}
                  @if ($preorder->pre_order_period->status->status_code == 1)
                    {{-- abierto --}}
                    <x-text-tag
                      color="emerald"
                      class="cursor-pointer"
                      >{{ $preorder->pre_order_period->status->status_name }}
                      <x-quest-icon title="{{ $preorder->pre_order_period->status->status_short_description }}" />
                    </x-text-tag>
                  @else
                    {{-- cerrado --}}
                    <x-text-tag
                      color="emerald"
                      class="cursor-pointer"
                      >{{ $preorder->pre_order_period->status->status_name }}
                      <x-quest-icon 
                        title="{{ $preorder->pre_order_period->status->status_short_description }}"/>
                    </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  {{ formatDateTime($preorder->pre_order_period->period_start_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ formatDateTime($preorder->pre_order_period->period_end_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td>
                  {{-- boton para descargar orden de compra --}}
                  @if ($preorder->order != null && $preorder->order_pdf != null)
                    <x-a-button
                      href="#"
                      wire:click="downloadPdfOrder({{ $preorder->id }})"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >descargar pdf
                      <x-svg-pdf-paper/>
                    </x-a-button>
                  @else
                    <span class="font-semibold text-neutral-400">-</span>
                  @endif
                </x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">
                    {{-- si el periodo NO esta cerrado --}}
                    @if ($preorder->pre_order_period->period_status_id !== $status_closed)
                      {{-- mientras la panaderia no apruebe, el proveedor puede editar --}}
                      @if (!$preorder->is_approved_by_buyer)
                        <x-a-button
                          wire:navigate
                          href="{{ route('quotations-preorders-respond', $preorder->id) }}"
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >
                          @if ($preorder->is_completed)
                            editar
                          @else
                            responder
                          @endif
                        </x-a-button>
                      {{-- aprobado, solo ver --}}
                      @else
                        <x-a-button
                          wire:navigate
                          href="{{ route('quotations-preorders-show', $preorder->id) }}"
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >ver
                        </x-a-button>
                      @endif
                    @else
                      <x-a-button
                        wire:navigate
                        href="{{ route('quotations-preorders-show', $preorder->id) }}"
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
                <td colspan="7">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $preorders->links() }}
      </x-slot:footer>

    </x-content-section>

    {{-- manejar evento para descargar pdf de orden --}}
    <script>
      document.addEventListener('livewire:initialized', () => {
          Livewire.on('downloadPdf', ({ url }) => {
              window.open(url, '_blank');
          });
      });
    </script>

  </article>
</div>
