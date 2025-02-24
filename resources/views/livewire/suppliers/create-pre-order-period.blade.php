<div>
  {{-- componente crear periodo de preordenes de compra --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear periodo de preordenes de compra">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-budgets-ranking', $period->id) }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver al ranking
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <span class="font-semibold text-neutral-800 capitalize">formulario</span>

        {{-- formulario del periodo --}}
        <form class="w-full flex flex-col gap-1">

          {{-- datos del periodo --}}
          <x-div-toggle x-data="{open: true}" title="datos del período de pre orden" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">Establezca el dia en que abrirá y cerrará el periodo.</span>
            </x-slot:subtitle>

            @error('period_*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            {{-- subgrupo --}}
            <div class="flex gap-1 min-h-fit">
              {{-- fecha de inicio --}}
              <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2 lg:w-1/4">
                <span>
                  <x-input-label for="period_start_at" class="font-normal">fecha de inicio</x-input-label>
                  <span class="text-red-600">*</span>
                </span>
                @error('period_start_at') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                <input
                  type="date"
                  name="period_start_at"
                  id="period_start_at"
                  wire:model="period_start_at"
                  min="{{ $min_date }}"
                  max="{{ $max_date }}"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
              </div>

              {{-- fecha de cierre --}}
              <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2 lg:w-1/4">
                <span>
                  <x-input-label for="period_end_at" class="font-normal">fecha de cierre</x-input-label>
                  <span class="text-red-600">*</span>
                </span>
                @error('period_end_at') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                <input
                  type="date"
                  name="period_end_at"
                  id="period_end_at"
                  wire:model="period_end_at"
                  min="{{ $min_date }}"
                  max="{{ $max_date }}"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>

              {{-- descripcion --}}
              <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2 lg:grow">
                <span>
                  <x-input-label for="period_short_description" class="font-normal">descripcion corta</x-input-label>
                </span>
                @error('period_short_description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                <input
                  type="text"
                  name="period_short_description"
                  id="period_short_description"
                  wire:model="period_short_description"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>
            </div>

          </x-div-toggle>

          {{-- preordenes generadas --}}
          <x-div-toggle x-data="{open: true}" title="Vista previa de pre ordenes a generar" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">se prepararan las siguientes {{ count($preview_preorders) }} pre ordenes segun los mejores precios de suministros y packs con los proveedores <span class="font-semibold text-emerald-600">activos.</span></span>
            </x-slot:subtitle>

            {{-- lista de preordenes --}}
            <div class="space-y-8 max-h-96 overflow-y-auto overflow-x-hidden">
              @foreach($preview_preorders as $preOrder)
                {{-- una pre orden --}}
                <div class="bg-white rounded-md shadow-md overflow-hidden">
                  <!-- Cabecera de la Pre-orden -->
                  <div class="bg-neutral-100 p-4 border-b">
                      <div class="flex justify-between items-center">
                          <div>
                              <h3 class="text-lg font-semibold text-neutral-800">
                                  código: {{ $preOrder['pre_order_data']['pre_order_code'] }}
                              </h3>
                              <p class="text-lg text-neutral-600">
                                fecha: {{ date('d-m-Y', strtotime($preOrder['pre_order_data']['current_date'])) }}
                              </p>
                          </div>
                          <div class="text-right">
                              <p class="text-lg font-bold text-neutral-800">
                                  total: ${{ number_format($preOrder['summary']['total_order'], 2) }}
                              </p>
                              <p class="text-sm text-neutral-600">
                                  {{ $preOrder['summary']['items_count'] }} ítems
                              </p>
                          </div>
                      </div>
                      <div class="mt-2">
                          <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-md">
                              {{ $preOrder['pre_order_data']['status'] }}
                          </span>
                          <span class="bg-neutral-200 text-neutral-800 text-sm font-medium ml-2 px-2.5 py-0.5 rounded-md">
                              referencia: {{ $preOrder['pre_order_data']['quotation_reference'] }}
                          </span>
                      </div>
                  </div>
                  {{-- detalle de preorden --}}
                  <x-div-toggle
                    x-data="{open: false}"
                    title="ver detalle"
                    class="mx-2"
                    >
                    <!-- Información del Proveedor -->
                    <div class="p-4 border-b">
                        <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Proveedor</h4>
                        <p class="font-medium">{{ $preOrder['supplier']['company_name'] }}</p>
                        <p class="text-sm text-neutral-600">CUIT: {{ $preOrder['supplier']['company_cuit'] }}</p>
                        <p class="text-sm text-neutral-600">
                            Contacto: {{ $preOrder['supplier']['contact_email'] }} | {{ $preOrder['supplier']['contact_phone'] }}
                        </p>
                    </div>

                    <!-- Suministros -->
                    @if(count($preOrder['provisions']) > 0)
                      <div class="p-4 border-b">
                        <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">
                          Suministros ({{ count($preOrder['provisions']) }})
                        </h4>
                        <div class="overflow-x-auto">
                          <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                              <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Suministro</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Marca/Tipo</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Cantidad</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Precio Unit.</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Total</th>
                              </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200">
                              @foreach($preOrder['provisions'] as $provision)
                                <tr>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                                    {{ $provision['provision_name'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                                    {{ $provision['trademark'] }} / {{ $provision['type'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                                    {{ $provision['quantity'] }} {{ $provision['measure'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                                    ${{ number_format($provision['unit_price'], 2) }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                                    ${{ number_format($provision['total_price'], 2) }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                            <tfoot class="bg-neutral-100">
                              <tr>
                                <td colspan="4" class="px-3 py-2 text-sm font-medium text-neutral-800 text-right">Subtotal Suministros:</td>
                                <td class="px-3 py-2 text-sm font-bold text-neutral-800 text-right">
                                    ${{ number_format($preOrder['summary']['total_provisions'], 2) }}
                                </td>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    @endif

                    <!-- Packs -->
                    @if(count($preOrder['packs']) > 0)
                        <div class="p-4 border-b">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">
                                Packs ({{ count($preOrder['packs']) }})
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack</th>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Tipo</th>
                                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($preOrder['packs'] as $pack)
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $pack['pack_name'] }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pack['trademark'] }} / {{ $pack['type'] }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                                    {{ $pack['quantity'] }} {{ $pack['measure'] }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                                    ${{ number_format($pack['unit_price'], 2) }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                                    ${{ number_format($pack['total_price'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="px-3 py-2 text-sm font-medium text-gray-900 text-right">Subtotal Packs:</td>
                                            <td class="px-3 py-2 text-sm font-bold text-gray-900 text-right">
                                                ${{ number_format($preOrder['summary']['total_packs'], 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Resumen de Totales -->
                    <div class="p-4 bg-neutral-100">
                      <div class="flex justify-end">
                        <div class="text-right space-y-1">
                          <p class="text-sm font-medium text-neutral-500">
                            subtotal suministros: ${{ number_format($preOrder['summary']['total_provisions'], 2) }}
                          </p>
                          <p class="text-sm font-medium text-neutral-500">
                            subtotal packs: ${{ number_format($preOrder['summary']['total_packs'], 2) }}
                          </p>
                          <p class="text-lg font-bold text-neutral-800">
                            total pre orden: ${{ number_format($preOrder['summary']['total_order'], 2) }}
                          </p>
                        </div>
                      </div>
                      {{-- seccion de botones --}}
                      <div class="mt-4 flex justify-end space-x-3"></div>
                    </div>

                  </x-div-toggle>
                </div>
              @endforeach
            </div>

          </x-div-toggle>

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">
        <!-- botones del formulario -->
        <div class="flex justify-end my-2 gap-2">

          <x-a-button
            wire:navigate
            href="{{ route('suppliers-budgets-periods-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            wire:confirm="¿Crear periodo de pre orden?, una vez creado no podrá modificarlo. Si la fecha de inicio es el dia de hoy, el periodo abrira inmediatamente y se contactará a los proveedores activos."
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
