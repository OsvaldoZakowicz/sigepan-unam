<div wire:poll>
  {{-- * componente POLL, consulta a la BD por actualizaciones cada 2.5 segundos --}}

  {{-- componente ver periodo de peticion de presupuestos --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver periodo presupuestario: {{ $period->period_code }}">

      <x-a-button wire:navigate href="{{ route('suppliers-budgets-periods-index') }}" bg_color="neutral-100"
        border_color="neutral-200" text_color="neutral-600">volver
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- titulo de encabezado --}}
        <div class="flex items-center justify-start w-full gap-2">
          <span>
            <span class="font-semibold">Estado actual:</span>&nbsp;
            {{-- estado del periodo --}}
            @switch($period->status->status_code)

            @case(0)
            {{-- programado --}}
            <x-text-tag title="{{ $period->status->status_short_description }}" color="neutral" class="cursor-pointer">
              {{ $period->status->status_name }}
              <x-quest-icon title="{{ $period->status->status_short_description }}" />
            </x-text-tag>
            @break

            @case(1)
            {{-- abierto --}}
            <x-text-tag title="{{ $period->status->status_short_description }}" color="emerald" class="cursor-pointer">
              {{ $period->status->status_name }}
              <x-quest-icon title="{{ $period->status->status_short_description }}" />
            </x-text-tag>
            @break

            @default
            {{-- cerrado --}}
            <x-text-tag title="{{ $period->status->status_short_description }}" color="red" class="cursor-pointer">{{
              $period->status->status_name }}
              <x-quest-icon title="{{ $period->status->status_short_description }}" />
            </x-text-tag>

            @endswitch
          </span>
          <span>
            <span class="font-semibold">Apertura:</span>&nbsp;
            {{ formatDateTime($period->period_start_at, 'd-m-Y') }}
          </span>
          <span>
            <span class="font-semibold">Cierre:</span>&nbsp;
            {{ formatDateTime($period->period_end_at, 'd-m-Y') }}
          </span>
        </div>

        {{-- botones con acciones --}}
        <div class="flex items-center justify-end w-2/3 gap-2">

          @if ($period->period_status_id === $scheduled)
          <x-a-button href="#" wire:click="openPeriod()" bg_color="emerald-600" border_color="emerald-600"
            text_color="neutral-100"
            wire:confirm="¿Abrir este período?, se enviarán pedidos de presupuesto a los proveedores activos de los suministros de interés">
            abrir ahora
          </x-a-button>
          @elseif ($period->period_status_id === $opened)
          <x-a-button href="#" wire:click="closePeriod()" bg_color="red-600" border_color="red-600"
            text_color="neutral-100"
            wire:confirm="¿Cerrar este período?, los proveedores con presupuesto sin responder quedarán fuera del periodo. Los presupuestos con respuestas se usaran en la actualizacion de precios para cada proveedor">
            cerrar ahora
          </x-a-button>
          @endif

        </div>

      </x-slot:header>

      <x-slot:content class="flex-col">

        {{-- aviso segun respuesta de proveedores --}}
        @if ($period_status === $closed and $count_quotations !== 0)
        <div class="flex items-center justify-between w-full p-1 border rounded-sm bg-emerald-100 border-emerald-300">
          <span class="text-emerald-800">
            <span class="font-semibold">¡Éxito!</span>
            <span>Se ha calculado una comparativa de precios y se han actualizado los precios por suministros para cada
              proveedor</span>
            <strong>usando los presupuestos respondidos</strong>
          </span>
          <x-a-button href="{{ route('suppliers-budgets-ranking', $period->id) }}" wire:navigate bg_color="emerald-600"
            border_color="emerald-600" text_color="neutral-100">ver
          </x-a-button>
        </div>
        @elseif ($period_status === $closed and $count_quotations === 0)
        <div class="flex items-center justify-between w-full p-1 bg-yellow-100 border border-yellow-500 rounded-sm">
          <span class="text-yellow-800">
            <span class="font-semibold">¡Aviso!</span>
            <span>¡No se han recibido presupuestos de los proveedores!</span>
          </span>
        </div>
        @endif

        {{-- suministros --}}
        <x-div-toggle x-data="{ open: false }" title="suministros de interés para este período:" class="p-2">

          {{-- leyenda --}}
          <x-slot:subtitle>
            <span>lista de suministros de los que se espera recibir presupuestos</span>
          </x-slot:subtitle>

          {{-- tabla de suministros --}}
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="w-12 text-end">
                  id
                </x-table-th>
                <x-table-th class="text-start">
                  nombre
                </x-table-th>
                <x-table-th class="text-start">
                  marca/tipo
                </x-table-th>
                <x-table-th class="text-end">
                  <span>volumen</span>
                  <x-quest-icon
                    title="kilogramos (Kg), gramos (g), litros (L), mililitros (mL), metros (M), centimetros (cm) o unidades (U)" />
                </x-table-th>
                <x-table-th class="text-end">
                  <span>cantidad a presupuestar</span>
                  <x-quest-icon title="cantidad de unidades de cada pack o de cada suministro que desea presupuestar" />
                </x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              @forelse ($period_provisions as $provision)
              <tr wire:key="{{ $provision->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $provision->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision->provision_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision->trademark->provision_trademark_name }}/{{ $provision->type->provision_type_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ convert_measure($provision->provision_quantity, $provision->measure) }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $provision->pivot->quantity }}
                </x-table-td>
              </tr>
              @empty
              <tr class="border">
                <td colspan="5">¡sin registros!</td>
              </tr>
              @endforelse
            </x-slot:tablebody>
          </x-table-base>
          {{-- paginacion --}}
          <div class="flex items-center justify-end w-full gap-1 mt-1">
            {{ $period_provisions->links() }}
          </div>
        </x-div-toggle>

        {{-- packs --}}
        <x-div-toggle x-data="{ open: false }" title="packs de interés para este período:" class="p-2">

          {{-- leyenda --}}
          <x-slot:subtitle>
            <span>lista de packs de los que se espera recibir presupuestos</span>
          </x-slot:subtitle>

          {{-- tabla de packs --}}
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="w-12 text-end">
                  id
                </x-table-th>
                <x-table-th class="text-start">
                  nombre
                </x-table-th>
                <x-table-th class="text-start">
                  marca
                </x-table-th>
                <x-table-th class="text-end">
                  <span>volumen</span>
                  <x-quest-icon
                    title="kilogramos (Kg), gramos (g), litros (L), mililitros (mL), metros (M), centimetros (cm) o unidades (U)" />
                </x-table-th>
                <x-table-th class="text-end">
                  <span>cantidad a presupuestar</span>
                  <x-quest-icon title="cantidad de unidades de cada pack o de cada suministro que desea presupuestar" />
                </x-table-th>
              </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
              @forelse ($period_packs as $pack)
              <tr wire:key="{{ $pack->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $pack->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $pack->pack_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $pack->provision->trademark->provision_trademark_name }}/{{
                  $pack->provision->type->provision_type_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ convert_measure($pack->pack_quantity, $pack->provision->measure) }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $pack->pivot->quantity }}
                </x-table-td>
              </tr>
              @empty
              <tr class="border">
                <td colspan="5">¡sin registros!</td>
              </tr>
              @endforelse
            </x-slot:tablebody>
          </x-table-base>
          {{-- paginacion --}}
          <div class="flex items-center justify-end w-full gap-1 mt-1">
            {{ $period_packs->links() }}
          </div>
        </x-div-toggle>

        {{-- presupuestos --}}
        <x-div-toggle x-data="{ open: true }" title="presupuestos solicitados en este período:" class="p-2">

          {{-- leyenda --}}
          <x-slot:subtitle>
            <span>lista de presupuestos a la espera de ser respondidos en el período</span>
          </x-slot:subtitle>

          {{-- tabla de presupuestos --}}
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-100">
                <x-table-th class="w-12 text-end">
                  id
                </x-table-th>
                <x-table-th class="text-start">
                  presupuesto
                </x-table-th>
                <x-table-th class="text-start">
                  proveedor
                </x-table-th>
                <x-table-th class="text-start">
                  estado
                </x-table-th>
                <x-table-th class="text-end">
                  <span>última respuesta</span>
                  <x-quest-icon title="última vez que el proveedor modificó los precios de este presupuesto" />
                </x-table-th>
                <x-table-th class="text-start">
                  presupuesto final
                  <x-quest-icon title="PDF disponible una vez que el presupuesto es respondido y el periodo ha cerrado" />
                </x-table-th>
                <x-table-th class="w-48 text-start">
                  acciones
                </x-table-th>
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
                  {{ $quotation->supplier->company_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- estado del presupuesto --}}
                  @if ($quotation->is_completed)
                  <x-text-tag title="el proveedor ha respondido" color="emerald" class="cursor-pointer">respondido
                    <x-quest-icon title="el proveedor ha respondido a este presupuesto" />
                  </x-text-tag>
                  @else
                  <x-text-tag title="el proveedor no ha respondido" color="neutral" class="cursor-pointer">sin responder
                    <x-quest-icon title="el proveedor aún no responde a este presupuesto" />
                  </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  @if ($quotation->is_completed)
                    {{ formatDateTime($quotation->updated_at, 'd-m-Y H:i') }} hs.
                  @else
                    <span class="font-semibold text-neutral-400">-</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($quotation->is_completed)
                    @if ($quotation->period->period_status_id == $closed)
                      @if ($this->hasSomeStock($quotation->id))
                        <x-a-button
                          wire:click="openPdf({{ $quotation->id }})"
                          href="#"
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >descargar PDF
                        </x-a-button>
                      @else
                        <span>sin stock</span>
                      @endif
                    @else
                      <span>no disponible</span>
                    @endif
                  @else
                    <span>sin respuesta</span>
                  @endif
                </x-table-td>
                <x-table-td>
                  <x-a-button wire:navigate href="{{ route('suppliers-budgets-response', $quotation->id) }}"
                    bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver presupuesto
                  </x-a-button>
                </x-table-td>
              </tr>
              @empty
              <tr class="border">
                <td colspan="2">¡sin registros hasta que el período comience!</td>
              </tr>
              @endforelse
            </x-slot:tablebody>
          </x-table-base>
          {{-- paginacion --}}
          <div class="flex items-center justify-end w-full gap-1 mt-1">
            {{ $period_quotations->links() }}
          </div>
        </x-div-toggle>

      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>

  {{-- manejar eventos --}}
  <script>

    /* evento: abrir pdf en nueva pestaña para visualizar */
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('openPdfInNewTab', ({ url }) => {
        window.open(url, '_blank');
      });
    });

  </script>
</div>