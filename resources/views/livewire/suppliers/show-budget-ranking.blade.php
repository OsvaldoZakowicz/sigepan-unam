<div>
  {{-- componente ranking de suministros por presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ranking de suministros del período presupuestario: {{ $period->period_code }}">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-budgets-periods-show', $period->id) }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver
      </x-a-button>

    </x-title-section>

    <x-content-section>

      <x-slot:header class="hidden">
      </x-slot:header>

      <x-slot:content class="flex flex-col gap-2 my-2">

        <span class="font-semibold">precios por proveedor:</span>

        @foreach ($quotations_ranking as $key => $provision)

          <div class="border border-neutral-300 rounded-md">
            {{-- suministro --}}
            <div class="bg-neutral-200 p-1">
              <span>
                <span class="font-semibold">{{ $provision['tipo'] }}:&nbsp;</span>
                <span>{{ $provision['nombre_suministro'] }},&nbsp;{{ $provision['marca'] }}&nbsp;</span>
                <span class="font-semibold">por:&nbsp;</span>
                <span>{{ $provision['volumen'] }}{{ $provision['volumen_tag'] }},&nbsp;</span>
                <span class="font-semibold">cantidad:&nbsp;</span>
                <span>{{ $provision['cantidad'] }}.</span>
              </span>
            </div>
            {{-- precios por proveedor --}}
            <div class="p-1">
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-50">
                    <x-table-th class="text-start">proveedor</x-table-th>
                    <x-table-th class="text-end w-48">precio</x-table-th>
                    <x-table-th class="text-start w-48">stock</x-table-th>
                    <x-table-th class="text-start w-48">acciones</x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @foreach ($provision['precios_por_proveedor'] as $precio)
                    <tr
                      wire:key="{{ $precio['id_proveedor'] }}"
                      class="border"
                      >
                      <x-table-td class="text-start">
                        {{ $precio['proveedor'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        @if ($provision['estadisticas']['precio_minimo'] === $precio['precio'])
                          <span class="font-light text-yellow-500">¡mejor precio!&nbsp;</span>
                        @endif
                        <span>$&nbsp;{{ $precio['precio'] }}</span>
                      </x-table-td>
                      <x-table-td class="text-start">
                        @if ($precio['tiene_stock'])
                          <span class="bg-emerald-200 text-emerald-700 font-light px-1">disponible</span>
                        @else
                          <span class="bg-red-200 text-red-600 font-light px-1">sin stock</span>
                        @endif
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{-- ver en presupuesto --}}
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
            {{-- estadisticas --}}
            {{-- <div class="flex justify-between items-center m-2 p-2 rounded-md bg-blue-100 border border-blue-300">
                <div>
                    <p class="font-semibold">Precio Mínimo</p>
                    <p>${{ $provision['estadisticas']['precio_minimo'] }}</p>
                </div>
                <div>
                    <p class="font-semibold">Precio Máximo</p>
                    <p>${{ $provision['estadisticas']['precio_maximo'] }}</p>
                </div>
                <div>
                    <p class="font-semibold">Precio Promedio</p>
                    <p>${{ $provision['estadisticas']['precio_promedio'] }}</p>
                </div>
                <div>
                    <p class="font-semibold">Cantidad de Proveedores</p>
                    <p>{{ $provision['estadisticas']['cantidad_proveedores'] }}</p>
                </div>
            </div> --}}
          </div>

        @endforeach

      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
