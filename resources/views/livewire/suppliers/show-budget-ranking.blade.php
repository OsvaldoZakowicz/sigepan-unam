<div>
  {{-- componente ranking de suministros por presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="ranking de suministros del período presupuestario: {{ $period->period_code }}">

     <div class="flex gap-2">
      <x-a-button
        onclick="window.history.back()"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        class="cursor-pointer"
        >volver
      </x-a-button>

      <x-a-button
        wire:click="createPreorders({{ $period->id }})"
        {{-- href="{{ route('suppliers-preorders-create', $period->id) }}" --}}
        class="cursor-pointer"
        >crear preordenes de compra
      </x-a-button>
     </div>

    </x-title-section>

    <x-content-section>

      <x-slot:header class="">
        <span class="font-semibold">precios por proveedor:</span>
      </x-slot:header>

      <x-slot:content class="flex flex-col gap-2 my-2">

        {{-- suministros --}}
        @foreach ($quotations_ranking['provisions'] as $key => $provision)
        <div class="border border-neutral-300 rounded-md">

            {{-- cabecera de un suministro --}}
            <div class="bg-neutral-200 p-1">
              <span>
                <span class="font-semibold">{{ $provision['tipo'] }}:&nbsp;</span>
                <span>{{ $provision['nombre_suministro'] }},&nbsp;{{ $provision['marca'] }}&nbsp;</span>
                <span class="font-semibold">por:&nbsp;</span>
                <span class="lowercase">{{ $provision['volumen'] }},&nbsp;</span>
              </span>
            </div>

            <div class="p-1">
              {{-- tabla de precios --}}
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-50">
                    <x-table-th class="text-start w-56">
                      <span>proveedor</span>
                    </x-table-th>
                    <x-table-th class="text-start w-28">
                      <span>stock</span>
                    </x-table-th>
                    <x-table-th class="text-end w-56">
                      <span>cantidad presupuestada</span>
                      <x-quest-icon title="cantidad pedida del suministro o pack en el presupuesto"/>
                    </x-table-th>
                    <x-table-th class="text-end w-56">
                      <span>precio unitario</span>
                      <x-quest-icon title="precio de un suministro o pack"/>
                    </x-table-th>
                    <x-table-th class="text-end w-56">
                      <span>precio total</span>
                      <x-quest-icon title="precio total de la cantidad presupuestada"/>
                    </x-table-th>
                    <x-table-th class="text-start w-48">
                      <span>acciones</span>
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @foreach ($provision['precios_por_proveedor'] as $precio)
                    <tr wire:key="{{ $precio['id_proveedor'] }}" class="border">
                      <x-table-td class="text-start">
                        {{ $precio['proveedor'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        @if ($precio['tiene_stock'])
                          <x-text-tag
                            color="emerald"
                            class="cursor-pointer text-xs"
                            title="el proveedor tiene stock disponible"
                            >disponible
                          </x-text-tag>
                        @else
                          <x-text-tag
                            color="red"
                            class="cursor-pointer text-xs"
                            title="el proveedor no tiene stock disponible"
                            >no disponible
                          </x-text-tag>
                        @endif
                      </x-table-td>
                      <x-table-td class="text-end">
                        <span>{{ $precio['cantidad'] }}</span>
                      </x-table-td>
                      <x-table-td class="text-end">
                        @if ($provision['estadisticas_precio_unitario']['precio_minimo'] === $precio['precio_unitario'])
                          <x-text-tag
                            color="orange"
                            class="cursor-pointer text-xs mx-1"
                            title="este es el mejor precio"
                            >¡mejor precio!
                          </x-text-tag>
                        @endif
                        <span>$&nbsp;{{ $precio['precio_unitario'] }}</span>
                      </x-table-td>
                      <x-table-td class="text-end">
                        @if ($provision['estadisticas_precio_total']['precio_minimo'] === $precio['precio_total'])
                          <x-text-tag
                            color="orange"
                            class="cursor-pointer text-xs mx-1"
                            title="este es el mejor precio"
                            >¡mejor precio!
                          </x-text-tag>
                        @endif
                        <span>$&nbsp;{{ $precio['precio_total'] }}</span>
                      </x-table-td>

                      <x-table-td class="text-start">
                        <x-a-button
                          href="{{ route('suppliers-budgets-response', $precio['id_presupuesto']) }}"
                          wire:navigate
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >ver presupuesto
                        </x-a-button>
                      </x-table-td>
                    </tr>
                  @endforeach
                </x-slot:tablebody>
              </x-table-base>
            </div>
          </div>

        @endforeach

        {{-- packs --}}
        @foreach ($quotations_ranking['packs'] as $key => $pack )
        <div class="border border-neutral-300 rounded-md">
            {{-- cabecera de un pack --}}
            <div class="bg-neutral-200 p-1">
              <span>
                <span class="font-semibold">{{ $pack['tipo'] }}:&nbsp;</span>
                <span>{{ $pack['nombre_pack'] }},&nbsp;{{ $pack['marca'] }}&nbsp;</span>
                <span class="font-semibold">por:&nbsp;</span>
                <span class="lowercase">{{ $pack['volumen'] }},&nbsp;</span>
              </span>
            </div>

            <div class="p-1">
              {{-- tabla de precios --}}
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-50">
                    <x-table-th class="text-start w-56">
                      <span>proveedor</span>
                    </x-table-th>
                    <x-table-th class="text-start w-28">
                      <span>stock</span>
                    </x-table-th>
                    <x-table-th class="text-end w-56">
                      <span>cantidad presupuestada</span>
                      <x-quest-icon title="cantidad pedida del suministro o pack en el presupuesto"/>
                    </x-table-th>
                    <x-table-th class="text-end w-56">
                      <span>precio unitario</span>
                      <x-quest-icon title="precio de un suministro o pack"/>
                    </x-table-th>
                    <x-table-th class="text-end w-56">
                      <span>precio total</span>
                      <x-quest-icon title="precio total de la cantidad presupuestada"/>
                    </x-table-th>
                    <x-table-th class="text-start w-48">
                      <span>acciones</span>
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @foreach ($pack['precios_por_proveedor'] as $precio)
                    <tr wire:key="{{ $precio['id_proveedor'] }}" class="border">
                      <x-table-td class="text-start">
                        {{ $precio['proveedor'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        @if ($precio['tiene_stock'])
                          <span class="bg-emerald-200 text-emerald-700 font-light px-1">disponible</span>
                        @else
                          <span class="bg-red-200 text-red-600 font-light px-1">sin stock</span>
                        @endif
                      </x-table-td>
                      <x-table-td class="text-end">
                        <span>{{ $precio['cantidad'] }}</span>
                      </x-table-td>
                      <x-table-td class="text-end">
                        @if ($pack['estadisticas_precio_unitario']['precio_minimo'] === $precio['precio_unitario'])
                          <span class="font-light text-yellow-500">¡mejor precio!&nbsp;</span>
                        @endif
                        <span>$&nbsp;{{ $precio['precio_unitario'] }}</span>
                      </x-table-td>
                      <x-table-td class="text-end">
                        @if ($pack['estadisticas_precio_total']['precio_minimo'] === $precio['precio_total'])
                          <span class="font-light text-yellow-500">¡mejor precio!&nbsp;</span>
                        @endif
                        <span>$&nbsp;{{ $precio['precio_total'] }}</span>
                      </x-table-td>

                      <x-table-td class="text-start">

                        <x-a-button
                          href="{{ route('suppliers-budgets-response', $precio['id_presupuesto']) }}"
                          wire:navigate
                          bg_color="neutral-100"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          >ver presupuesto
                        </x-a-button>
                      </x-table-td>
                    </tr>
                  @endforeach
                </x-slot:tablebody>
              </x-table-base>
            </div>
          </div>

        @endforeach

      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
