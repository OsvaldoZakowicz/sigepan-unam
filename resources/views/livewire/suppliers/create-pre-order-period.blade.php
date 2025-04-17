<div>
  {{-- componente crear periodo de preordenes de compra --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear periodo de preordenes de compra">

      @if ($period  !== null)

        <x-a-button
          wire:navigate
          href="{{ route('suppliers-budgets-ranking', $period->id) }}"
          bg_color="neutral-100"
          border_color="neutral-200"
          text_color="neutral-600"
          >volver al ranking
        </x-a-button>

      @else

        <x-a-button
          wire:navigate
          href="{{ route('suppliers-preorders-index') }}"
          bg_color="neutral-100"
          border_color="neutral-200"
          text_color="neutral-600"
          >volver
        </x-a-button>

      @endif

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

          {{-- si no existe priodo presupustario previo, mostrar seccion para creacion manual de pre ordenes --}}
          {{-- permitir buscar y elegir suministros y packs, luego, proveedor a contactar --}}
          @if ($period === null)
            <x-div-toggle x-data="{open: true}" title="suministros y packs a pre ordenar" class="p-2">

              <x-slot:subtitle>
                <span>se presupuestarán suministros y packs de proveedores <x-text-tag color="emerald">activos</x-text-tag> </span>
              </x-slot:subtitle>

              <x-slot:messages>
                @error('items')
                  <span class="text-red-400">¡hay errores en esta sección!</span>
                @enderror
              </x-slot:messages>

              {{-- necesito buscar --}}
              @livewire('suppliers.search-provision-period')

              {{-- necesito listar lo elegido en la busqueda --}}
              {{-- tabla dinamica con campos de formulario para cantidad y proveedor --}}
              {{-- lista de suministros elegidos --}}
              <div class="flex flex-col gap-1 w-full">
                <span class="font-semibold capitalize">lista de suministros y/o packs elegidos para pre ordenar</span>
                <div class="max-h-72 overflow-y-auto overflow-x-hidden">
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="text-end w-12">
                          id
                        </x-table-th>
                        <x-table-th class="text-start">
                          nombre
                        </x-table-th>
                        <x-table-th class="text-start">
                          marca
                        </x-table-th>
                        <x-table-th class="text-start">
                          tipo
                        </x-table-th>
                        <x-table-th class="text-end">
                          <span>cantidad</span>
                          <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
                        </x-table-th>
                        <x-table-th class="text-end">
                          <span>unidades a pre ordenar</span>
                          <span class="text-red-400">*</span>
                          <x-quest-icon title="cantidad de unidades que desea pre ordenar" />
                        </x-table-th>
                        <x-table-th class="text-end">
                          <span>proveedor a contactar</span>
                          <span class="text-red-400">*</span>
                          <x-quest-icon title="proveedor al cual pre ordenar y ultimo precio unitario asignado o presupuestado" />
                        </x-table-th>
                        <x-table-th class="text-start w-16">
                          quitar
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($items as $key => $item)
                        @if ($item['provision'] !== null)
                          {{-- renglon es suministro --}}
                          <tr wire:key="{{ $key }}">
                            <x-table-td class="text-end">
                              {{ $item['provision']->id }}
                            </x-table-td>
                            <x-table-td
                              title="{{ $item['provision']->provision_short_description }}"
                              class="cursor-pointer text-start">
                              {{ $item['provision']->provision_name }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{ $item['provision']->trademark->provision_trademark_name }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{ $item['provision']->type->provision_type_name }}
                            </x-table-td>
                            <x-table-td class="text-end">
                              {{ convert_measure($item['provision']->provision_quantity, $item['provision']->measure) }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{-- input cantidad --}}
                              @error('items.'.$key.'.quantity')<span class="text-red-400 text-xs capitalize">{{ $message }}</span>@enderror
                              <div class="flex justify-start items-center">
                                {{-- cantidad a pre ordenar --}}
                                <x-text-input
                                  id="items_{{ $key }}_quantity"
                                  wire:model.defer="items.{{ $key }}.quantity"
                                  type="number"
                                  min="1"
                                  max="99"
                                  step="1"
                                  placeholder="cantidad ..."
                                  class="w-full text-right"/>
                              </div>
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{-- select proveedor --}}
                              @error('items.'.$key.'.supplier_id')<span class="text-red-400 text-xs capitalize">{{ $message }}</span>@enderror
                              <div class="flex justify-start items-center">
                                {{-- proveedor a elegir --}}
                                <select
                                  id="items.{{ $key }}.supplier_id"
                                  name="items_{{ $key }}_supplier_id"
                                  wire:model.defer="items.{{ $key }}.supplier_id"
                                  class="p-1 w-full text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                  >
                                  <option value="">seleccione un proveedor ...</option>
                                  @forelse ($item['available_suppliers'] as $supplier)
                                    <option value="{{ $supplier['id'] }}">{{ $supplier['company_name'] }} - $&nbsp;{{ $supplier['price'] }}</option>
                                  @empty
                                    <option value="">seleccione un proveedor ...</option>
                                  @endforelse
                                </select>
                              </div>
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{-- boton para quitar de la lista --}}
                              <span
                                title="quitar de la lista."
                                wire:click="removeFromItemsList({{ $key }})"
                                class="font-semibold cursor-pointer leading-none p-1 bg-red-200 text-neutral-600 border-red-300 rounded-sm"
                                >&times;
                              </span>
                            </x-table-td>
                          </tr>
                        @else
                          {{-- renglon es pack --}}
                          <tr wire:key="{{ $key }}">
                            <x-table-td class="text-end">
                              {{ $item['pack']->id }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{ $item['pack']->pack_name }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{ $item['pack']->provision->trademark->provision_trademark_name }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{ $item['pack']->provision->type->provision_type_name }}
                            </x-table-td>
                            <x-table-td class="text-end">
                              {{ convert_measure($item['pack']->pack_quantity, $item['pack']->provision->measure) }}
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{-- input cantidad --}}
                              @error('items.'.$key.'.quantity')<span class="text-red-400 text-xs capitalize">{{ $message }}</span>@enderror
                              <div class="flex justify-start items-center">
                                {{-- cantidad a pre ordenar --}}
                                <x-text-input
                                  id="items_{{ $key }}_quantity"
                                  wire:model.defer="items.{{ $key }}.quantity"
                                  type="number"
                                  min="1"
                                  max="99"
                                  step="1"
                                  placeholder="cantidad ..."
                                  class="w-full text-right"/>
                              </div>
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{-- select proveedor --}}
                              @error('items.'.$key.'.supplier_id')<span class="text-red-400 text-xs capitalize">{{ $message }}</span>@enderror
                              <div class="flex justify-start items-center">
                                {{-- proveedor a elegir --}}
                                <select
                                  id="items.{{ $key }}.supplier_id"
                                  name="items_{{ $key }}_supplier_id"
                                  wire:model.defer="items.{{ $key }}.supplier_id"
                                  class="p-1 w-full text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                  >
                                  <option value="">seleccione un proveedor ...</option>
                                  @forelse ($item['available_suppliers'] as $supplier)
                                    <option value="{{ $supplier['id'] }}">{{ $supplier['company_name'] }} - $&nbsp;{{ $supplier['price'] }}</option>
                                  @empty
                                    <option value="">seleccione un proveedor ...</option>
                                  @endforelse
                                </select>
                              </div>
                            </x-table-td>
                            <x-table-td class="text-start">
                              {{-- boton para quitar de la lista --}}
                              <span
                                title="quitar de la lista."
                                wire:click="removeFromItemsList({{ $key }})"
                                class="font-semibold cursor-pointer leading-none p-1 bg-red-200 text-neutral-600 border-red-300 rounded-sm"
                                >&times;
                              </span>
                            </x-table-td>
                          </tr>
                        @endif
                      @empty
                        <tr class="border">
                          <td colspan="8">¡lista vacia!</td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                </div>
              </div>

            </x-div-toggle>
          @endif

          {{-- preordenes a generar --}}
          <x-div-toggle x-data="{open: true}" title="Vista previa de pre ordenes a generar" class="p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">se prepararan las siguientes {{ count($preview_preorders) }} pre ordenes segun los mejores precios de suministros y packs con los proveedores <x-text-tag color="emerald" >activos</x-text-tag> </span>
            </x-slot:subtitle>

            {{-- lista de preordenes --}}
            <div class="space-y-8 max-h-96 overflow-y-auto overflow-x-hidden">
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-50">
                    <x-table-th class="text-end w-12">
                      #
                    </x-table-th>
                    <x-table-th class="text-start">
                      código
                    </x-table-th>
                    <x-table-th class="text-end">
                      fecha tentativa
                    </x-table-th>
                    <x-table-th class="text-start">
                      presupuesto de origen
                    </x-table-th>
                    <x-table-th class="text-start">
                      proveedor
                    </x-table-th>
                    <x-table-th class="text-end">
                      cantidad de items
                    </x-table-th>
                    <x-table-th class="text-end">
                      costo total
                    </x-table-th>
                    <x-table-th class="text-start">
                      acciones
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @forelse ($preview_preorders as $key => $preorder)
                    <tr>
                      <x-table-td class="text-end w-12">
                        {{ $key+1 }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $preorder['pre_order_data']['pre_order_code'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ formatDateTime($preorder['pre_order_data']['current_date'], 'd-m-Y') }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $preorder['pre_order_data']['quotation_reference'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $preorder['supplier']['company_name'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ $preorder['summary']['items_count'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        ${{ number_format($preorder['summary']['total_order'], 2) }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{-- vista previa en un modal --}}
                        <x-a-button
                          wire:click="showModal({{ $key }})"
                          bg_color="neutral-200"
                          border_color="neutral-200"
                          text_color="neutral-600"
                          class="cursor-pointer"
                          >vista previa
                        </x-a-button>
                      </x-table-td>
                    </tr>
                  @empty
                    <tr>
                      <x-table-td colspan="8">
                        <span>¡Sin vistas previas hasta que se definan las pre ordenes!</span>
                      </x-table-td>
                    </tr>
                  @endforelse
                </x-slot:tablebody>
              </x-table-base>
            </div>

          </x-div-toggle>

          {{-- modal pre orden --}}
          @if($showing_preorder_modal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modal">
              <div class="relative top-20 mx-auto w-2/3 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center p-4 border-b">
                  <h3 class="text-lg font-semibold">Vista Previa de Pre Orden</h3>
                  <button type="button" wire:click="closeModal()" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                @if($selected_preorder)
                  {{-- una pre orden --}}
                  <div class="bg-white overflow-hidden">
                    <!-- Cabecera de la Pre-orden -->
                    <div class="bg-neutral-100 p-4 border-b">
                      <div class="flex justify-between items-center">
                        <div>
                          <h3 class="text-lg text-neutral-800">
                            código: <span class="font-semibold uppercase">{{ $selected_preorder['pre_order_data']['pre_order_code'] }}</span>
                          </h3>
                          <p class="text-lg text-neutral-600">
                            fecha: {{ formatDateTime($selected_preorder['pre_order_data']['current_date'], 'd-m-Y') }}
                          </p>
                        </div>
                      </div>
                      <div class="mt-2 flex gap-2 justify-start items-center">
                        <x-text-tag
                          color="neutral"
                          class="cursor-pointer"
                          >pendiente
                          <x-quest-icon title="estado inicial de la pre orden"/>
                        </x-text-tag>
                        @if ($selected_preorder['pre_order_data']['quotation_reference'] !== null)
                          <x-text-tag
                            color="neutral"
                            class="cursor-pointer"
                            >referencia: <span class="uppercase text-xs font-semibold">{{ $selected_preorder['pre_order_data']['quotation_reference'] }}</span>
                          </x-text-tag>
                        @endif
                      </div>
                    </div>

                    <!-- informacion del emisor y proveedor -->
                    <div class="flex justify-start items-start gap-2 p-4 border-b">
                      {{-- emisor --}}
                      <div class="p-4">
                        <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Emisor</h4>
                        <p class="font-medium">panaderia</p>
                        <p class="text-sm text-neutral-600">CUIT: 99999999999</p>
                        <p class="text-sm text-neutral-600">Contacto: email@ejemplo.com | Tel: 3758252525</p>
                      </div>
                      {{-- proveedor --}}
                      <div class="p-4">
                        <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Proveedor</h4>
                        <p class="font-medium">{{ $selected_preorder['supplier']['company_name'] }}</p>
                        <p class="text-sm text-neutral-600">CUIT: {{ $selected_preorder['supplier']['company_cuit'] }}</p>
                        <p class="text-sm text-neutral-600">Contacto: {{ $selected_preorder['supplier']['contact_email'] }} | Tel: {{ $selected_preorder['supplier']['contact_phone'] }}</p>
                      </div>
                    </div>

                    {{-- items --}}
                    <div class="p-4 border-b">
                      <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Detalle:</h4>
                      <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200">
                          <thead class="bg-neutral-50">
                            <tr>
                              <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"
                                >#
                              </th>
                              <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"
                                >Suministro/Pack
                              </th>
                              <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"
                                >Marca/Tipo/cantidad
                              </th>
                              <th
                                scope="col"
                                class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                                ">Unidades a pre ordenar
                              </th>
                              <th
                                scope="col"
                                class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                                ">Precio Unit.
                              </th>
                              <th
                                scope="col"
                                class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                                ">Sub Total
                              </th>
                            </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-neutral-200">
                            {{-- key --}}
                            @php
                              $keynumber = 1;
                            @endphp
                            {{-- suministros --}}
                            @if(count($selected_preorder['provisions']) > 0)

                              @foreach ($selected_preorder['provisions'] as $provision)
                                <tr>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                    {{ $keynumber++ }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                                    {{ $provision['provision_name'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                                    <span>
                                      <span>{{ $provision['trademark'] }} / {{ $provision['type'] }}</span>
                                      <span class="lowercase"> / de {{ $provision['volumen'] }}</span>
                                    </span>
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                                    {{ $provision['quantity'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                                    ${{ number_format($provision['unit_price'], 2) }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                                    ${{ number_format($provision['total_price'], 2) }}
                                  </td>
                                </tr>
                              @endforeach

                            @endif

                            {{-- packs --}}
                            @if(count($selected_preorder['packs']) > 0)

                              @foreach ($selected_preorder['packs'] as $pack)
                                <tr>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                    {{ $keynumber++ }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                                    {{ $pack['pack_name'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                                    <span>
                                      <span>{{ $pack['trademark'] }} / {{ $pack['type'] }}</span>
                                      <span class="lowercase"> / de {{ $pack['volumen'] }}</span>
                                    </span>
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                                    {{ $pack['quantity'] }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                                    ${{ number_format($pack['unit_price'], 2) }}
                                  </td>
                                  <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                                    ${{ number_format($pack['total_price'], 2) }}
                                  </td>
                                </tr>
                              @endforeach

                            @endif
                          </tbody>
                          <tfoot class="bg-neutral-100">
                            <tr>
                              <td colspan="5" class="px-3 py-2 text-lg font-medium text-neutral-800 text-right">Total:</td>
                              <td class="px-3 py-2 text-lg font-bold text-neutral-800 text-right">
                                ${{ number_format($selected_preorder['summary']['total_order'], 2) }}
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="flex justify-end p-4 bg-gray-50">
                  <x-a-button
                    wire:click="closeModal()"
                    bg_color="neutral-200"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    class="cursor-pointer"
                    >Cerrar
                  </x-a-button>
                </div>
              </div>
            </div>
          @endif

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">

        <!-- botones del formulario -->
        <div class="flex justify-end my-2 gap-2">

          <x-a-button
            wire:navigate
            href="{{ route('suppliers-preorders-index') }}"
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
