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
                <x-text-tag
                  title="{{ $preorder_period->status->status_short_description }}"
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder_period->status->status_name }}
                </x-text-tag>
                @break

              @case(1)
                {{-- abierto --}}
                <x-text-tag
                  title="{{ $preorder_period->status->status_short_description }}"
                  color="emerald"
                  class="cursor-pointer"
                  >{{ $preorder_period->status->status_name }}
                </x-text-tag>
                @break

              @default
                {{-- cerrado --}}
                <x-text-tag
                  title="{{ $preorder_period->status->status_short_description }}"
                  color="red"
                  class="cursor-pointer"
                  >{{ $preorder_period->status->status_name }}
                </x-text-tag>

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
              wire:confirm="¿Abrir este período?, se enviarán las pre ordenes a los proveedores activos de los suministros y packs de interés"
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

        @if ($preorder_period->quotation_period_id == null)

          {{--
            todo: suministros y packs de interes
            cuando el periodo de pre orden NO proviene de un previo
            periodo presupuestario
          --}}

          {{-- suministros de interes --}}
          <x-div-toggle x-data="{ open: false }" title="suministros de interés para este período:" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span>lista de suministros pre ordenados en el período</span>
            </x-slot:subtitle>

            {{-- tabla de suministros --}}
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th class="text-end w-12">id</x-table-th>
                  <x-table-th class="text-start w-56">nombre</x-table-th>
                  <x-table-th class="text-start">marca</x-table-th>
                  <x-table-th class="text-end">
                    <span>cantidad</span>
                    <x-quest-icon title=""/>
                  </x-table-th>
                  <x-table-th class="text-end">
                    <span>cantidad pre ordenada</span>
                    <x-quest-icon title="cantidad de unidades de cada suministro que fue pre ordenado"/>
                  </x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>

              </x-slot:tablebody>
            </x-table-base>
            {{-- paginacion --}}
            <div class="w-full flex justify-end items-center gap-1 mt-1">

            </div>
          </x-div-toggle>

          {{-- packs de interes --}}
          <x-div-toggle x-data="{ open: false }" title="packs de interés para este período:" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span>lista de packs pre ordenados en el período</span>
            </x-slot:subtitle>

            {{-- tabla de packs --}}
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th class="text-end w-12">id</x-table-th>
                  <x-table-th class="text-start w-56">nombre</x-table-th>
                  <x-table-th class="text-start">marca</x-table-th>
                  <x-table-th class="text-end">
                    <span>cantidad</span>
                    <x-quest-icon title=""/>
                  </x-table-th>
                  <x-table-th class="text-end">
                    <span>cantidad pre ordenada</span>
                    <x-quest-icon title="cantidad de unidades de cada pack que fue pre ordenado"/>
                  </x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
              </x-slot:tablebody>
            </x-table-base>
            {{-- paginacion --}}
            <div class="w-full flex justify-end items-center gap-1 mt-1">
            </div>
          </x-div-toggle>

        @else

          {{--
            suministros y packs de interes
            cuando el periodo de pre orden viene de un periodo presupuestario previo
          --}}

          <div class="flex justify-between items-center mb-2 p-1 border border-neutral-200 bg-neutral-100 rounded-sm">

            <span class="">
              <span>pre ordenes creadas a partir del ranking de presupuestos</span>
              <span>obtenidos en el periodo presupuestario: <span class="font-semibold">{{ $preorder_period->quotation_period->period_code }}</span>.</span>
            </span>

            <x-a-button
              wire:navigate
              href="{{ route('suppliers-budgets-ranking', $preorder_period->quotation_period->id) }}"
              bg_color="neutral-100"
              border_color="neutral-200"
              text_color="neutral-600"
              >ver ranking
            </x-a-button>

          </div>

          {{-- suministros de interes --}}
          <x-div-toggle x-data="{ open: false }" title="suministros de interés para este período:" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span>lista de suministros pre ordenados en el período</span>
            </x-slot:subtitle>

            {{-- tabla de suministros --}}
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th class="text-end w-12">id</x-table-th>
                  <x-table-th class="text-start w-56">nombre</x-table-th>
                  <x-table-th class="text-start">marca / tipo</x-table-th>
                  <x-table-th class="text-end">
                    <span>cantidad</span>
                    <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
                  </x-table-th>
                  <x-table-th class="text-end">
                    <span>unidades pre ordenadas</span>
                    <x-quest-icon title="cantidad de unidades de cada suministro que fue pre ordenado"/>
                  </x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                @forelse ($quotations_ranking['provisions'] as $provision)
                  <tr>
                    <x-table-td class="text-end">{{ $provision['id_suministro'] }}</x-table-td>
                    <x-table-td class="text-start">{{ $provision['nombre_suministro'] }}</x-table-td>
                    <x-table-td class="text-start">{{ $provision['marca'] }} / {{ $provision['tipo'] }}</x-table-td>
                    <x-table-td class="text-end">{{ $provision['volumen'] }}</x-table-td>
                    <x-table-td class="text-end">{{ $provision['cantidad'] }}</x-table-td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5">¡sin registros!</td>
                  </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
            {{-- paginacion --}}
            {{-- <div class="w-full flex justify-end items-center gap-1 mt-1"></div> --}}
          </x-div-toggle>

          {{-- packs de interes --}}
          <x-div-toggle x-data="{ open: false }" title="packs de interés para este período:" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span>lista de packs pre ordenados en el período</span>
            </x-slot:subtitle>

            {{-- tabla de packs --}}
            <x-table-base>
              <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                  <x-table-th class="text-end w-12">id</x-table-th>
                  <x-table-th class="text-start w-56">nombre</x-table-th>
                  <x-table-th class="text-start">marca / tipo</x-table-th>
                  <x-table-th class="text-end">
                    <span>cantidad</span>
                    <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
                  </x-table-th>
                  <x-table-th class="text-end">
                    <span>unidades pre ordenadas</span>
                    <x-quest-icon title="cantidad de unidades de cada pack que fue pre ordenado"/>
                  </x-table-th>
                </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                @forelse ($quotations_ranking['packs'] as $pack)
                  <tr>
                    <x-table-td class="text-end">{{ $pack['id_pack'] }}</x-table-td>
                    <x-table-td class="text-start">{{ $pack['nombre_pack'] }}</x-table-td>
                    <x-table-td class="text-start">{{ $pack['marca'] }} / {{ $provision['tipo'] }}</x-table-td>
                    <x-table-td class="text-end">{{ $pack['volumen'] }}</x-table-td>
                    <x-table-td class="text-end">{{ $pack['cantidad'] }}</x-table-td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5">¡sin registros!</td>
                  </tr>
                @endforelse
              </x-slot:tablebody>
            </x-table-base>
            {{-- paginacion --}}
            {{-- <div class="w-full flex justify-end items-center gap-1 mt-1"></div> --}}
          </x-div-toggle>

        @endif

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
                <x-table-th class="text-start">estado de la pre orden</x-table-th>
                <x-table-th class="text-start">evaluación</x-table-th>
                <x-table-th class="text-start">orden de compra</x-table-th>
                <x-table-th class="text-end">
                  <span>última respuesta</span>
                  <x-quest-icon title="última vez que el proveedor modificó los precios de este presupuesto"/>
                </x-table-th>
                <x-table-th class="text-start w-24">acciones</x-table-th>
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
                    {{-- estado de pre orden --}}
                    @if ($preorder->is_completed)
                      <x-text-tag
                        color="emerald"
                        class="cursor-pointer"
                        >respondido
                        <x-quest-icon title="el proveedor ha respondido"/>
                      </x-text-tag>
                    @else
                      {{-- proveedor no respondio --}}
                      <x-text-tag
                        color="neutral"
                        class="cursor-pointer"
                        >sin responder
                        <x-text-tag title="el proveedor no ha respondido"/>
                      </x-text-tag>
                    @endif
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{-- evaluacion --}}
                    @if ($preorder->status === $status_pending)
                      <x-text-tag
                        color="neutral"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="pendiente de evaluación"/>
                      </x-text-tag>
                    @elseif ($preorder->status === $status_approved)
                      <x-text-tag
                        color="emerald"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="aprobó esta preorden para crear una orden definitiva"/>
                      </x-text-tag>
                    @else
                      <x-text-tag
                        color="red"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="esta pre orden fue rechazada por una de las partes"/>
                      </x-text-tag>
                    @endif
                  </x-table-td>
                  <x-table-td class="text-end">

                    {{-- ver pdf --}}
                    <x-a-button
                      href="#"
                      wire:click="openPdfOrder({{ $preorder->id }})"
                      bg_color="neutral-200"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver pdf
                    </x-a-button>

                  </x-table-td>
                  <x-table-td class="text-end">
                    @if ($preorder->is_completed)
                      {{ formatDateTime($preorder->updated_at, 'd-m-Y') }}
                    @else
                      <span class="font-semibold text-neutral-400">-</span>
                    @endif
                  </x-table-td>
                  <x-table-td>

                    {{-- si el proveedor completo, puedo ver --}}
                    @if ($preorder->is_completed)
                      <x-a-button
                        wire:navigate
                        href="{{ route('suppliers-preorders-response', $preorder->id) }}"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >ver
                      </x-a-button>
                    @endif

                  </x-table-td>
                </tr>
              @empty
                <tr class="border">
                  <td colspan="8">¡sin registros hasta que el período comience!</td>
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

    {{-- manejar evento para mostrar orden en pdf en nueva ventana --}}
    <script>
      document.addEventListener('livewire:initialized', () => {
          Livewire.on('openPdfInNewTab', ({ url }) => {
              window.open(url, '_blank');
          });
      });
    </script>

  </article>
</div>
