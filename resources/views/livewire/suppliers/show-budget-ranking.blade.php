<div>
  {{-- componente ranking de suministros por presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="ranking de suministros del período presupuestario: {{ $period->period_code }}">

      <div class="flex gap-2">
        <x-a-button wire:navigate href="{{ route('suppliers-budgets-periods-show', $period->id) }}"
          bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600" class="cursor-pointer">volver
        </x-a-button>

        <x-a-button wire:click="createPreorders({{ $period->id }})"
          wire:confirm="¿Desea crear pre ordenes de compra a partir de este ranking de presupuestos?. Se pre ordenará cada suministro al mejor precio."
          class="cursor-pointer">crear preordenes de compra
        </x-a-button>
      </div>

    </x-title-section>

    <x-content-section>

      <x-slot:header class="">
        <span class="font-semibold">precios por proveedor:</span>
      </x-slot:header>

      <x-slot:content class="flex flex-col gap-1 my-2">

        {{-- suministros --}}
        @foreach ($quotations_ranking['provisions'] as $key => $provision)
        <x-div-toggle x-data="{ open: true }" class="p-1">
          <x-slot:title>
            <span>
              <span class="font-semibold">{{ $provision['tipo'] }}:&nbsp;</span>
              <span>{{ $provision['nombre_suministro'] }},&nbsp;{{ $provision['marca'] }}&nbsp;</span>
              <span class="font-semibold">por:&nbsp;</span>
              <span class="lowercase">{{ $provision['volumen'] }},&nbsp;</span>
            </span>
          </x-slot:title>
          {{-- tabla de precios --}}
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-50">
                <x-table-th class="w-56 text-start">
                  <span>proveedor</span>
                </x-table-th>
                <x-table-th class="text-start w-28">
                  <span>stock</span>
                </x-table-th>
                <x-table-th class="w-56 text-end">
                  <span>unidades presupuestadas</span>
                  <x-quest-icon title="cantidad de unidades pedidas del suministro o pack en el presupuesto" />
                </x-table-th>
                <x-table-th class="w-56 text-end">
                  <span>precio unitario</span>
                  <x-quest-icon title="precio de un suministro o pack" />
                </x-table-th>
                <x-table-th class="w-56 text-end">
                  <span>precio total</span>
                  <x-quest-icon title="precio total de la cantidad presupuestada" />
                </x-table-th>
                <x-table-th class="w-48 text-start">
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
                  <x-text-tag color="emerald" class="text-xs cursor-pointer"
                    title="el proveedor tiene stock disponible">disponible
                  </x-text-tag>
                  @else
                  <x-text-tag color="red" class="text-xs cursor-pointer" title="el proveedor no tiene stock disponible">
                    no disponible
                  </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  <span>{{ $precio['cantidad'] }}</span>
                </x-table-td>
                <x-table-td class="text-end">
                  @if ($provision['estadisticas_precio_unitario']['precio_minimo'] === $precio['precio_unitario'])
                  <x-text-tag color="orange" class="mx-1 text-xs cursor-pointer" title="este es el mejor precio">
                    ¡mejor precio!
                  </x-text-tag>
                  @endif
                  <span>$&nbsp;{{ toMoneyFormat($precio['precio_unitario']) }}</span>
                </x-table-td>
                <x-table-td class="text-end">
                  @if ($provision['estadisticas_precio_total']['precio_minimo'] === $precio['precio_total'])
                  <x-text-tag color="orange" class="mx-1 text-xs cursor-pointer" title="este es el mejor precio">
                    ¡mejor precio!
                  </x-text-tag>
                  @endif
                  <span>$&nbsp;{{ toMoneyFormat($precio['precio_total']) }}</span>
                </x-table-td>

                <x-table-td class="text-start">
                  <x-a-button href="{{ route('suppliers-budgets-response', $precio['id_presupuesto']) }}" wire:navigate
                    bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver
                    presupuesto
                  </x-a-button>
                </x-table-td>
              </tr>
              @endforeach
            </x-slot:tablebody>
          </x-table-base>
        </x-div-toggle>
        @endforeach

        {{-- packs --}}
        @foreach ($quotations_ranking['packs'] as $key => $pack )
        <x-div-toggle x-data="{ open: true }" class="p-1">
          <x-slot:title>
            <span>
              <span class="font-semibold">{{ $pack['tipo'] }}:&nbsp;</span>
              <span>{{ $pack['nombre_pack'] }},&nbsp;{{ $pack['marca'] }}&nbsp;</span>
              <span class="font-semibold">por:&nbsp;</span>
              <span class="lowercase">{{ $pack['volumen'] }},&nbsp;</span>
            </span>
          </x-slot:title>
          {{-- tabla de precios --}}
          <x-table-base>
            <x-slot:tablehead>
              <tr class="border bg-neutral-50">
                <x-table-th class="w-56 text-start">
                  <span>proveedor</span>
                </x-table-th>
                <x-table-th class="text-start w-28">
                  <span>stock</span>
                </x-table-th>
                <x-table-th class="w-56 text-end">
                  <span>unidades presupuestadas</span>
                  <x-quest-icon title="cantidad de unidades pedidas del suministro o pack en el presupuesto" />
                </x-table-th>
                <x-table-th class="w-56 text-end">
                  <span>precio unitario</span>
                  <x-quest-icon title="precio de un suministro o pack" />
                </x-table-th>
                <x-table-th class="w-56 text-end">
                  <span>precio total</span>
                  <x-quest-icon title="precio total de la cantidad presupuestada" />
                </x-table-th>
                <x-table-th class="w-48 text-start">
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
                  <x-text-tag color="emerald" class="text-xs cursor-pointer"
                    title="el proveedor tiene stock disponible">disponible
                  </x-text-tag>
                  @else
                  <x-text-tag color="red" class="text-xs cursor-pointer" title="el proveedor no tiene stock disponible">
                    no disponible
                  </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  <span>{{ $precio['cantidad'] }}</span>
                </x-table-td>
                <x-table-td class="text-end">
                  @if ($pack['estadisticas_precio_unitario']['precio_minimo'] === $precio['precio_unitario'])
                  <x-text-tag color="orange" class="mx-1 text-xs cursor-pointer" title="este es el mejor precio">
                    ¡mejor precio!
                  </x-text-tag>
                  @endif
                  <span>$&nbsp;{{ toMoneyFormat($precio['precio_unitario']) }}</span>
                </x-table-td>
                <x-table-td class="text-end">
                  @if ($pack['estadisticas_precio_total']['precio_minimo'] === $precio['precio_total'])
                  <x-text-tag color="orange" class="mx-1 text-xs cursor-pointer" title="este es el mejor precio">
                    ¡mejor precio!
                  </x-text-tag>
                  @endif
                  <span>$&nbsp;{{ toMoneyFormat($precio['precio_total']) }}</span>
                </x-table-td>

                <x-table-td class="text-start">

                  <x-a-button href="{{ route('suppliers-budgets-response', $precio['id_presupuesto']) }}" wire:navigate
                    bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver
                    presupuesto
                  </x-a-button>
                </x-table-td>
              </tr>
              @endforeach
            </x-slot:tablebody>
          </x-table-base>
        </x-div-toggle>
        @endforeach

      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>