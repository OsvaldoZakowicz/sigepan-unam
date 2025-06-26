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

        @if ($errors->any())
          <span>hay problemas de validacion!</span>
        @endif

        {{-- formulario del periodo --}}
        <form class="flex flex-col w-full gap-1">

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
              <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2 lg:w-1/4">
                <span>
                  <x-input-label for="period_start_at" class="font-normal">fecha de inicio</x-input-label>
                  <span class="text-red-600">*</span>
                </span>
                @error('period_start_at') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
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
              <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2 lg:w-1/4">
                <span>
                  <x-input-label for="period_end_at" class="font-normal">fecha de cierre</x-input-label>
                  <span class="text-red-600">*</span>
                </span>
                @error('period_end_at') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
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
              <div class="flex flex-col w-full gap-1 min-h-fit md:w-1/2 lg:grow">
                <span>
                  <x-input-label for="period_short_description" class="font-normal">descripcion corta</x-input-label>
                </span>
                @error('period_short_description') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                <input
                  type="text"
                  name="period_short_description"
                  id="period_short_description"
                  wire:model="period_short_description"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>
            </div>

          </x-div-toggle>

          {{-- si no existe priodo presupuestario previo, mostrar seccion para creacion manual de pre ordenes --}}
          {{-- permitir buscar y elegir suministros y packs, luego, proveedor a contactar --}}
          @if ($period === null)
            <x-div-toggle x-data="{open: true}" title="suministros y packs a pre ordenar" class="p-2">

              <x-slot:subtitle>
                <span>se pre ordenarán suministros y packs de proveedores <x-text-tag color="emerald">activos</x-text-tag></span>
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
              <div class="flex flex-col w-full gap-1">
                <span>Lista de suministros y/o packs elegidos para pre ordenar</span>

                {{-- errores en la lista --}}
                <div class="mb-2">
                  @if($errors->has('items') || $errors->has('items.*.supplier_id') || $errors->has('items.*.quantity'))
                    <ul class="text-sm text-red-400 list-none">
                      @error('items')
                        <li>{{ $message }}</li>
                      @enderror

                      @error('items.*.supplier_id')
                        <li>{{ $message }}</li>
                      @enderror

                      @error('items.*.quantity')
                        <li>{{ $message }}</li>
                      @enderror
                    </ul>
                  @endif
                </div>

                {{-- lista --}}
                <div class="overflow-x-hidden overflow-y-auto max-h-72">
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
                        <x-table-th class="text-start">
                          tipo
                        </x-table-th>
                        <x-table-th class="text-end">
                          <span>volumen</span>
                          <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)"/>
                        </x-table-th>
                        <x-table-th class="text-end">
                          <span>cantidad a pre ordenar</span>
                          <span class="text-red-400">*</span>
                          <x-quest-icon title="cantidad de unidades que desea pre ordenar" />
                        </x-table-th>
                        <x-table-th class="text-end">
                          <span>proveedor a contactar</span>
                          <span class="text-red-400">*</span>
                          <x-quest-icon title="proveedor al cual pre ordenar y ultimo precio unitario asignado o presupuestado" />
                        </x-table-th>
                        <x-table-th class="w-16 text-start">
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
                              <div class="flex items-center justify-start">
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
                              <div class="flex items-center justify-start">
                                {{-- proveedor a elegir --}}
                                <select
                                  id="items.{{ $key }}.supplier_id"
                                  name="items_{{ $key }}_supplier_id"
                                  wire:model.defer="items.{{ $key }}.supplier_id"
                                  class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
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
                                class="p-1 font-semibold leading-none bg-red-200 border-red-300 rounded-sm cursor-pointer text-neutral-600"
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
                              <div class="flex items-center justify-start">
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
                              <div class="flex items-center justify-start">
                                {{-- proveedor a elegir --}}
                                <select
                                  id="items.{{ $key }}.supplier_id"
                                  name="items_{{ $key }}_supplier_id"
                                  wire:model.defer="items.{{ $key }}.supplier_id"
                                  class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
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
                                class="p-1 font-semibold leading-none bg-red-200 border-red-300 rounded-sm cursor-pointer text-neutral-600"
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

                  {{-- vaciar lista --}}
                  <div class="flex items-center justify-end w-full mt-2">
                    <x-a-button
                      href="#"
                      wire:click="vaciarLista()"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >vaciar lista
                    </x-a-button>
                  </div>

                </div>
              </div>

            </x-div-toggle>
          @endif

          {{-- preview preordenes a generar --}}
          @if ($period !== null)
            <x-div-toggle x-data="{open: true}" title="Vista previa de pre ordenes a generar" class="p-2">

              {{-- leyenda --}}
              <x-slot:subtitle>
                <span class="text-sm text-neutral-600">se prepararan las siguientes {{ count($preview_preorders) }} pre ordenes segun los mejores precios de suministros y packs del ranking, con los proveedores <x-text-tag color="emerald" >activos</x-text-tag> </span>
              </x-slot:subtitle>

              {{-- lista de preordenes --}}
              <div class="space-y-8 overflow-x-hidden overflow-y-auto max-h-96">
                <x-table-base>
                  <x-slot:tablehead>
                    <tr class="border bg-neutral-50">
                      <x-table-th class="w-12 text-end">
                        #
                      </x-table-th>
                      <x-table-th class="text-start">
                        código
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
                      <x-table-th class="text-end">
                        fecha de envío
                        <x-quest-icon title="fecha en la que esta pre orden se enviará al provedor" />
                      </x-table-th>
                      <x-table-th class="text-start">
                        acciones
                      </x-table-th>
                    </tr>
                  </x-slot:tablehead>
                  <x-slot:tablebody>
                    @forelse ($preview_preorders as $key => $preorder)
                      <tr>
                        <x-table-td class="w-12 text-end">
                          {{ $key+1 }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $preorder['pre_order_data']['pre_order_code'] }}
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
                          ${{ toMoneyFormat($preorder['summary']['total_order']) }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{ formatDateTime($preorder['pre_order_data']['current_date'], 'd-m-Y') }}
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
                          <span>¡No se ha podido capturar datos del ranking!</span>
                        </x-table-td>
                      </tr>
                    @endforelse
                  </x-slot:tablebody>
                </x-table-base>
              </div>

            </x-div-toggle>

            {{-- modal pre orden --}}
            @if($showing_preorder_modal)           
              <div class="fixed inset-0 z-50 flex items-center justify-center w-full h-full overflow-y-auto bg-neutral-400 bg-opacity-40">
                <div class="w-5/6 p-5 transition-all transform bg-white border rounded-md shadow-lg">
                  <div class="text-start">
                    <h3 class="text-lg font-medium leading-6 capitalize text-neutral-800">
                      Vista previa de pre orden.</span>
                    </h3>
                    <div class="flex flex-col">
                      <span class="mb-4 font-semibold uppercase">para:</span>
                      <span>
                        <span class="font-semibold">Proveedor:</span>
                        <span>{{ $selected_preorder['supplier']['company_name'] }}</span>
                        <span class="font-semibold">CUIT:</span>
                        <span>CUIT: {{ $selected_preorder['supplier']['company_cuit'] }}</span>
                        <span class="font-semibold">Tel:</span>
                        <span>{{ $selected_preorder['supplier']['contact_phone'] }}</span>
                        <span class="font-semibold">Correo:</span>
                        <span>{{ $selected_preorder['supplier']['contact_email'] }}</span>
                      </span>
                    </div>
                    <div class="mt-4 overflow-x-auto overflow-y-auto max-h-72">
                      <x-table-base>
                        <x-slot:tablehead>
                          <tr class="border bg-neutral-100">
                            <x-table-th class="w-12 text-end">
                              #
                            </x-table-th>
                            <x-table-th class="text-start">
                              Suministro/Pack
                            </x-table-th>
                            <x-table-th class="text-start">
                              Marca/Tipo/Volumen
                            </x-table-th>
                            <x-table-th class="text-end">
                              Cantidad a preordenar
                            </x-table-th>
                            <x-table-th class="text-end">
                              $Precio unitario
                            </x-table-th>
                            <x-table-th class="text-end">
                              $Subtotal
                            </x-table-th>
                          </tr>
                        </x-slot:tablehead>
                        <x-slot:tablebody>
                          {{-- key --}}
                          @php
                            $keynumber = 1;
                          @endphp
                          {{-- suministros --}}
                          @if(count($selected_preorder['provisions']) > 0)
                            @foreach ($selected_preorder['provisions'] as $provision)
                              <tr class="border">
                                <x-table-td class="text-end">
                                  {{ $keynumber++ }}
                                </x-table-td>
                                <x-table-td class="text-start">
                                  {{ $provision['provision_name'] }}
                                </x-table-td>
                                <x-table-td class="text-start">
                                  <span>
                                    <span>{{ $provision['trademark'] }}/{{ $provision['type'] }}</span>
                                    <span class="lowercase">/{{ $provision['volumen'] }}</span>
                                  </span>
                                </x-table-td>
                                <x-table-td class="text-end">
                                  {{ $provision['quantity'] }}
                                </x-table-td>
                                <x-table-td class="text-end">
                                  ${{ toMoneyFormat($provision['unit_price']) }}
                                </x-table-td>
                                <x-table-td class="text-end">
                                  ${{ toMoneyFormat($provision['total_price']) }}
                                </x-table-td>
                              </tr>
                            @endforeach
                          @endif
                          {{-- packs --}}
                          @if(count($selected_preorder['packs']) > 0)
                            @foreach ($selected_preorder['packs'] as $pack)
                              <tr class="border">
                                <x-table-td class="text-end">
                                  {{ $keynumber++ }}
                                </x-table-td>
                                <x-table-td class="text-start">
                                  {{ $pack['pack_name'] }}
                                </x-table-td>
                                <x-table-td class="text-start">
                                  <span>
                                    <span>{{ $pack['trademark'] }}/{{ $pack['type'] }}</span>
                                    <span class="lowercase">/{{ $pack['volumen'] }}</span>
                                  </span>
                                </x-table-td>
                                <x-table-td class="text-end">
                                  {{ $pack['quantity'] }}
                                </x-table-td>
                                <x-table-td class="text-end">
                                  ${{ toMoneyFormat($pack['unit_price']) }}
                                </x-table-td>
                                <x-table-td class="text-end">
                                  ${{ toMoneyFormat($pack['total_price']) }}
                                </x-table-td>
                              </tr>
                            @endforeach
                          @endif
                          <tfoot>
                            <tr>
                              <x-table-td colspan="5" class="font-semibold capitalize text-end">$Total</x-table-td>
                              <x-table-td class="font-semibold text-end">
                                ${{ toMoneyFormat($selected_preorder['pre_order_data']['total_amount']) }}
                              </x-table-td>
                            </tr>
                          </tfoot>
                        </x-slot:tablebody>
                      </x-table-base>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                      <x-btn-button
                        type="button"                        
                        wire:click="closeModal()"
                        color="neutral"
                        >Cerrar
                      </x-btn-button>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endif

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">

        <!-- botones del formulario -->
        <div class="flex justify-end gap-2 my-2">

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
            wire:confirm="¿Crear periodo de pre orden?. Si la fecha de inicio es el dia de hoy, el periodo abrira inmediatamente y se contactará a los proveedores activos."
            >guardar
          </x-btn-button>

        </div>

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
