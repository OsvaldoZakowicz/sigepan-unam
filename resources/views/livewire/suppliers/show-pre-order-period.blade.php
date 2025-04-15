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
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder_period->status->status_name }}
                  <x-quest-icon title="{{ $preorder_period->status->status_short_description }}"/>
                </x-text-tag>
                @break

              @case(1)
                {{-- abierto --}}
                <x-text-tag
                  color="emerald"
                  class="cursor-pointer"
                  >{{ $preorder_period->status->status_name }}
                  <x-quest-icon title="{{ $preorder_period->status->status_short_description }}"/>
                </x-text-tag>
                @break

              @default
                {{-- cerrado --}}
                <x-text-tag
                  color="red"
                  class="cursor-pointer"
                  >{{ $preorder_period->status->status_name }}
                  <x-quest-icon title="{{ $preorder_period->status->status_short_description }}"/>
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

        {{-- aviso cuando el periodo cerro, para acciones extra --}}
        {{-- existen suministros y packs sin cubrir, o finalizado correctamente --}}
        @if ($period_status === $closed)
          @if ($has_uncovered_items)

            {{-- alerta fija --}}
            <div class="flex justify-between items-center p-1 border border-yellow-200 bg-yellow-100 rounded-sm">
              <div class="flex flex-col">
                <span class="text-yellow-800">
                  <span class="font-semibold">¡atención!</span>
                  <span>el periodo ha cerrado y existen suministros y packs pre ordenados que no fueron cubiertos en su totalidad!</span>
                </span>
              </div>
            </div>

            {{-- seccion con tabla de faltantes --}}
            <x-div-toggle x-data="{ open: true }" title="suministros y packs no cubiertos" class="relative border-yellow-200 p-2 mb-6">

              <x-slot:subtitle>puede crear un nuevo periodo de pre ordenes para cubrir faltantes!</x-slot:subtitle>

              <div class="absolute top-0 right-0 px-2 flex justify-end">
                <x-a-button
                  href="{{ route('suppliers-preorders-create') }}"
                  target="_blank"
                  >crear nuevo periodo
                </x-a-button>
              </div>

              {{-- tabla de faltantes --}}
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-100">
                    <x-table-th class="text-end w-12">
                      <span>id</span>
                    </x-table-th>
                    <x-table-th class="text-start">
                      <span>nombre</span>
                    </x-table-th>
                    <x-table-th class="text-start">
                      <span>marca/tipo</span>
                    </x-table-th>
                    <x-table-th class="text-end">
                      <span>cantidad</span>
                      <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
                    </x-table-th>
                    <x-table-th class="text-end">
                      <span>unidades faltantes</span>
                      <x-quest-icon title="cantidad de unidades de cada suministro que no pudieron cubrirse en las ordenes de compra finales"/>
                    </x-table-th>
                    <x-table-th class="text-start">
                      <span>proveedor contactado</span>
                      <x-quest-icon title="proveedor que fue contactado en este período"/>
                    </x-table-th>
                    <x-table-th class="text-start">
                      <span>proveedores alternativos</span>
                      <x-quest-icon title="indica si existen proveedores alternativos a los que pedir los faltantes, según el ranking presupuestario inicial"/>
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  {{-- suministros --}}
                  @foreach ($uncovered_provisions as $uncovered_provision)
                    <tr>
                      <x-table-td class="text-end">
                        {{ $uncovered_provision['id_suministro'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $uncovered_provision['nombre_suministro'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $uncovered_provision['marca_suministro'] }}/{{ $uncovered_provision['tipo_suministro'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ convert_measure($uncovered_provision['cantidad_suministro'], $uncovered_provision['unidad_suministro']) }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ $uncovered_provision['cantidad_faltante'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $uncovered_provision['proveedor_contactado']->company_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{-- puede haber mas de 1 --}}
                        <div x-data="{ open: false }" class="relative">

                          {{-- texto con desplegable de lista --}}
                          <button @click="open = !open" class="flex items-center text-blue-700 hover:text-blue-900">
                            @if (count($uncovered_provision['alternative_suppliers']) > 0)
                              <span>Ver {{ count($uncovered_provision['alternative_suppliers']) }} proveedores</span>
                            @else
                              <span>Ninguno</span>
                            @endif
                          </button>

                          {{-- lista desplegable --}}
                          <ul x-show="open"
                              @click.away="open = false"
                              class="absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-md shadow-lg">
                            @forelse ($uncovered_provision['alternative_suppliers'] as $alt_supplier)
                              <li class="p-2 hover:bg-gray-100">
                                <span class="block text-sm">{{ $alt_supplier['proveedor'] }}</span>
                                <span class="block text-xs text-gray-500">Precio unitario: ${{ $alt_supplier['precio_unitario'] }}</span>
                              </li>
                            @empty
                              <li class="p-2 text-gray-500">No hay proveedores alternativos</li>
                            @endforelse
                          </ul>
                        </div>
                      </x-table-td>
                    </tr>
                  @endforeach
                  {{-- packs --}}
                  @foreach ($uncovered_packs as $uncovered_pack)
                    <tr>
                      <x-table-td class="text-end">
                        {{ $uncovered_pack['id_pack'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $uncovered_pack['nombre_pack'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $uncovered_pack['marca_pack'] }}/{{ $uncovered_pack['tipo_pack'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ convert_measure($uncovered_pack['cantidad_pack'], $uncovered_pack['unidad_pack']) }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ $uncovered_pack['cantidad_faltante'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $uncovered_pack['proveedor_contactado']->company_name }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{-- puede haber mas de 1 --}}
                        @forelse ($uncovered_pack['alternative_suppliers'] as $alt_supplier)
                          <span>{{ $alt_supplier['proveedor'] }}</span>
                        @empty
                          <span>ninguno</span>
                        @endforelse
                      </x-table-td>
                    </tr>
                  @endforeach
                </x-slot:tablebody>
              </x-table-base>

            </x-div-toggle>

          @else

            {{-- alerta fija --}}
            <div class="flex justify-between items-center mb-2 p-1 border border-emerald-200 bg-emerald-100 rounded-sm">
              <span class="text-emerald-800">
                <span class="font-semibold">¡Éxito!</span>
                <span>se han pedido y cubierto todos los suministros y packs pre ordenados!</span>
              </span>
            </div>

          @endif
        @endif

        @if ($preorder_period->quotation_period_id == null)

          {{--
            todo: suministros y packs de interes
            * cuando el periodo de pre orden NO proviene de un previo
            * periodo presupuestario
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
            * suministros y packs de interes
            * cuando el periodo de pre orden viene de un periodo presupuestario previo
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
                  <x-table-th class="text-start">nombre</x-table-th>
                  <x-table-th class="text-start">marca/tipo</x-table-th>
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
                    <x-table-td class="text-start">{{ $provision['marca'] }}/{{ $provision['tipo'] }}</x-table-td>
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
                  <x-table-th class="text-start">nombre</x-table-th>
                  <x-table-th class="text-start">marca/tipo</x-table-th>
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
                    <x-table-td class="text-start">{{ $pack['marca'] }}/{{ $provision['tipo'] }}</x-table-td>
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
                <x-table-th class="text-end">última respuesta<x-quest-icon title="última vez que el proveedor modificó su respuesta en la pre orden"/></x-table-th>
                <x-table-th class="text-start">orden de compra<x-quest-icon title="PDF disponible cuando la pre orden es aprobada y se solicita al proveedor una orden de compra definitiva" /></x-table-th>
                <x-table-th class="text-start w-36">acciones</x-table-th>
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
                    {{ $preorder->supplier->company_name }}
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
                      <x-text-tag
                        color="neutral"
                        class="cursor-pointer"
                        >sin responder
                        <x-quest-icon title="el proveedor no ha respondido"/>
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
                        <x-quest-icon title="aprobó esta pre orden para crear una orden definitiva"/>
                      </x-text-tag>
                    @else
                      <x-text-tag
                        color="red"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="esta pre orden fue rechazada por alguna de las partes"/>
                      </x-text-tag>
                    @endif
                  </x-table-td>
                  <x-table-td class="text-end">
                    {{-- ultima respuesta --}}
                    @if ($preorder->is_completed)
                      <span>{{ formatDateTime($preorder->updated_at, 'd-m-Y H:i:s') }} hs.</span>
                    @else
                      <span class="font-semibold text-neutral-400">-</span>
                    @endif
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{-- ver pdf --}}
                    @if ($preorder->order != null && $preorder->order_pdf != null)
                      <x-a-button
                        href="#"
                        wire:click="openPdfOrder({{ $preorder->id }})"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        title="ver orden de compras y descargar pdf"
                        >ver pdf
                        <x-svg-pdf-paper/>
                      </x-a-button>
                    @else
                      <span class="font-semibold text-neutral-400">-</span>
                    @endif
                  </x-table-td>
                  <x-table-td>
                    {{-- acciones --}}
                    @if ($preorder->is_completed)
                      <x-a-button
                        wire:navigate
                        href="{{ route('suppliers-preorders-response', $preorder->id) }}"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >ver preorden
                      </x-a-button>
                    @else
                      <span class="font-semibold text-neutral-400">-</span>
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

      <x-slot:footer class="">
      </x-slot:footer>

    </x-content-section>

    {{-- manejar eventos --}}
    <script>

      /* evento: abrir pdf en nueva pestaña para visualizar */
      document.addEventListener('livewire:initialized', () => {
          Livewire.on('openPdfInNewTab', ({ url }) => {
              window.open(url, '_blank');
          });
      });

    </script>

  </article>
</div>
