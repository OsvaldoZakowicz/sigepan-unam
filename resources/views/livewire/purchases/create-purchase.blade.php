<div>
  {{-- componente registrar compra --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="registrar compra"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        {{-- formulario de la compra --}}
        <form class="w-full flex flex-col gap-1">

          {{-- datos de la compra --}}
          <x-fieldset-base tema="registrar compra" class="w-full p-2">

            <div class="w-full flex justify-start items-start gap-4">

              {{-- columna de inputs 1 --}}
              <div class="flex flex-col gap-4 w-1/2">
                <div class="flex gap-4 min-h-fit">
                  {{-- proveedor --}}
                  <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                    <span>
                      <label for="formdt_supplier_id">proveedor</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('formdt_supplier_id')
                      <span class="text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                    {{-- con preorden, obtenemos el proveedor --}}
                    {{-- sin preorden, listamos proveedores para elegirlos --}}
                    @if ($with_preorder)
                      <input
                        type="text"
                        value="{{ $order_data['supplier']->company_name }}"
                        readonly
                        class="p-1 w-full text-sm bg-neutral-100 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                    @else
                      <select
                        id="formdt_supplier_id"
                        name="formdt_supplier_id"
                        wire:model="formdt_supplier_id"
                        class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                        >
                        <option value="">seleccione un proveedor ...</option>
                        @forelse ($suppliers as $supplier)
                          <option
                            wire:click="getProvisionsAndPacksForSupplier()"
                            value="{{ $supplier->id }}"
                            >{{ $supplier->company_name }}
                          </option>
                        @empty
                          <option value="">sin proveedores registrados.</option>
                        @endforelse
                      </select>
                    @endif

                  </div>
                  {{-- fecha de compra --}}
                  {{-- con preorden, la fecha de compra no debe ser anterior a la fecha de preorden --}}
                  {{-- sin preorden, la fecha de compra puede ser cualquiera --}}
                  <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                    <span>
                      <label for="formdt_purchase_date">fecha de compra</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('formdt_purchase_date')
                      <span class="text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                    <input
                      type="date"
                      wire:model="formdt_purchase_date"
                      id="formdt_purchase_date"
                      name="formdt_purchase_date"
                      min="{{ $date_min }}"
                      max="{{ $date_max }}"
                      class="p-1 w-full text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>
                </div>
                @if ($with_preorder)
                  <div class="flex gap-4 min-h-fit">
                    {{-- orden de compra relacionada --}}
                    <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                      <span>
                        <label for="formdt_order_code">orden de compra asociada</label>
                      </span>
                      @error('formdt_order_code')
                        <span class="text-red-400 text-xs">{{ $message }}</span>
                      @enderror
                      {{-- codigo de preorden asociado --}}
                      <input
                        type="text"
                        value="{{ $order_data['order_code'] }}"
                        readonly
                        class="p-1 w-full text-sm bg-neutral-100 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                    </div>
                    {{-- fecha de la orden de compra relacionada --}}
                    <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                      <span>
                        <label for="formdt_order_date">fecha de la orden</label>
                      </span>
                      @error('formdt_order_date')
                        <span class="text-red-400 text-xs">{{ $message }}</span>
                      @enderror
                      {{-- codigo de preorden asociado --}}
                        <input
                          type="text"
                          value="{{ $order_data['order_date'] }}"
                          readonly
                          class="p-1 w-full text-sm bg-neutral-100 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                        />
                    </div>
                  </div>
                @endif
              </div>

              {{-- columna de inputs 2 --}}
              <div class="flex flex-col gap-4 w-1/2">
                <div class="flex gap-4 min-h-fit">
                  {{-- subir algun comprobante? --}}
                  <div class="flex flex-col gap-1 min-h-fit w-full md:w-1/2">
                    {{-- todo --}}
                  </div>
                  {{-- preview comprobante? --}}
                  {{-- todo --}}
                </div>
              </div>

            </div>

          </x-fieldset-base>

          {{-- lista de suministros y packs comprados --}}
          <x-div-toggle x-data="{ open: true }" title="detalles de la compra" class="w-full p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              @if ($with_preorder)
                <span class="text-sm text-neutral-600">suministros y/o packs comprados</span>
              @else
                <span class="text-sm text-neutral-600">Complete los detalles de la compra indicando suministros y/o packs del proveedor</span>
              @endif
            </x-slot:subtitle>

            @error('formdt_purchase_items*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            {{-- si existe preorden, listar suministros y packs de la pre orden, y el total --}}
            {{-- si no existe preorden, buscar y elegir suministros y/o packs --}}
            @if ($with_preorder)
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200">
                  <thead class="bg-neutral-50">
                    <tr>
                      <th
                        scope="col"
                        class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider"
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
                        >Marca/Tipo/volumen
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                        ">Cantidad comprada
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                        ">Volumen total
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                        ">Precio Unit.
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                        ">Subtotal
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-neutral-200">
                    @foreach($formdt_purchase_items as $key => $item)
                      {{-- suministros --}}
                      @if ($item['item_type'] === $this->getProvisionType())
                        <tr>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                            {{ $key+1 }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                            {{ $item['name'] }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                            <span>
                              <span> {{$item['trademark']}} / {{ $item['type'] }} </span>
                              <span class="lowercase"> / de {{ $item['unit_volume']['value'] }}{{ $item['unit_volume']['symbol'] }} </span>
                            </span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>{{ $item['item_count'] }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>{{ $item['total_volume']['value'] }}{{ $item['total_volume']['symbol'] }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>${{ number_format($item['unit_price'], 2) }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                            <span>${{ number_format($item['subtotal_price'], 2) }}</span>
                          </td>
                        </tr>
                      @endif
                      {{-- packs --}}
                      @if ($item['item_type'] === $this->getPackType())
                        <tr>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                            {{ $key+1 }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                            {{ $item['name'] }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                            <span>
                              <span> {{$item['trademark']}} / {{ $item['type'] }} </span>
                              <span class="lowercase"> / de {{ $item['unit_volume']['value'] }}{{ $item['unit_volume']['symbol'] }} </span>
                            </span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>{{ $item['item_count'] }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>{{ $item['total_volume']['value'] }}{{ $item['total_volume']['symbol'] }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>${{ number_format($item['unit_price'], 2) }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                            <span>${{ number_format($item['subtotal_price'], 2) }}</span>
                          </td>
                        </tr>
                      @endif
                    @endforeach
                  </tbody>
                  <tfoot class="bg-neutral-100">
                    <tr>
                      <td colspan="6" class="px-3 py-2 text-normal font-medium text-neutral-800 text-right">Total:</td>
                      <td class="px-3 py-2 text-normal font-bold text-neutral-800 text-right">
                        ${{ number_format($order_data['total_price'], 2) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            @else
              {{-- una vz elegido un proveedor, buscar entre sus suministros y packs para el detalle --}}
              @if ($formdt_supplier_id)
                {{-- componente de busqueda de suministros y packs --}}
                @livewire('purchases.search-provision', ['supplier_id' => $formdt_supplier_id])

                {{-- tabla con vista de los resultados de busqueda e inputs dinamicos por fila --}}
                <span class="mb-2">Detalle de compra:</span>
                @error('formdt_purchase_items*')
                  <span class="text-red-400">{{ $message }}</span>
                @enderror
                <div class="overflow-x-auto">
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="bg-neutral-100 border">
                        <x-table-th class="text-end">
                          #
                        </x-table-th>
                        <x-table-th class="text-start">
                          Suministro/Pack
                        </x-table-th>
                        <x-table-th class="text-start">
                          Marca/Tipo/Volumen
                        </x-table-th>
                        <x-table-th class="text-end">
                          Cantidad comprada
                        </x-table-th>
                        <x-table-th class="text-end">
                          Volumen total
                          <x-quest-icon title="kilogramos (kg), gramos (g), litros (l), mililitros (ml), metro (m), centimetro (cm), unidad (u)" />
                        </x-table-th>
                        <x-table-th class="text-end">
                          Precio unit.
                          <x-quest-icon title="precio unitario del proveedor para el suministro o pack" />
                        </x-table-th>
                        <x-table-th class="text-end">
                          Subtotal
                          <x-quest-icon title="subtotal de la compra del suministro o pack, formato de ejemplo 1200.45: 'mil doscientos con 45 centavos'" />
                        </x-table-th>
                        <x-table-th class="text-start">
                          quitar
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($formdt_purchase_items as $key => $item )
                        <tr wire:key="{{ $key }}" class="border">
                          <x-table-td class="text-end">
                            <span>{{ $key+1 }}</span>
                          </x-table-td>
                          <x-table-td class="text-start">
                            <span>{{ $item['name'] }}</span>
                          </x-table-td>
                          <x-table-td class="text-start">
                            <span>
                              <span> {{$item['trademark']}} / {{ $item['type'] }} </span>
                              <span class="lowercase"> / de {{ $item['unit_volume']['value'] }}{{ $item['unit_volume']['symbol'] }} </span>
                            </span>
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{-- input para cantidad --}}
                            <input
                              type="number"
                              min="1"
                              step="1"
                              wire:model.live="formdt_purchase_items.{{ $key }}.item_count"
                              wire:change="updateItemQuantity({{ $key }}, $event.target.value)"
                              class="w-20 px-2 py-1 text-right text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                            />
                          </x-table-td>
                          <x-table-td class="text-end">
                            <span>{{ $item['total_volume']['value'] }}{{ $item['total_volume']['symbol'] }}</span>
                          </x-table-td>
                          <x-table-td class="text-end">
                            <span>${{ toMoneyFormat($item['unit_price']) }}</span>
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{-- input para subtotal --}}
                            {{-- puede completarse manualmente --}}
                            <span>$</span>
                            <input
                              type="text"
                              wire:model.live="formdt_purchase_items.{{ $key }}.subtotal_price"
                              wire:change="formatItemSubtotal({{ $key }}, $event.target.value)"
                              pattern="[0-9.,]*" {{-- solo numeros con . y , --}}
                              class="w-36 px-2 py-1 text-right text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                            />
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{-- boton para quitar de la lista --}}
                            <span
                              title="quitar de la lista."
                              wire:click="removeItem({{ $key }})"
                              class="font-semibold cursor-pointer leading-none p-1 bg-red-200 text-neutral-600 border-red-300 rounded-sm"
                              >&times;
                            </span>
                          </x-table-td>
                        </tr>
                        @if ($loop->last)
                          <tr class="border">
                            <x-table-td class="font-semibold text-end capitalize" colspan="6">Total</x-table-td>
                            <x-table-td class="font-semibold text-end">${{ toMoneyFormat($total) }}</x-table-td>
                            <x-table-td></x-table-td>
                          </tr>
                        @endif
                      @empty
                        <tr class="border">
                          <x-table-td colspan="8">¡sin registros!</x-table-td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                  <div class="w-full flex justify-end items-center mt-2">
                    <x-a-button
                      href="#"
                      wire:click="resetList()"
                      bg_color="neutral-100"
                      border_color="neutral-300"
                      text_color="neutral-600"
                      >vaciar lista
                    </x-a-button>
                  </div>
                </div>
              @else
                <span>Seleccione un proveedor para comenzar</span>
              @endif
            @endif

          </x-div-toggle>

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">
        <!-- botones del formulario -->
        <div class="flex justify-end my-2 gap-2">

          <x-a-button
            wire:navigate
            href="{{ route('purchases-purchases-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            wire:confirm="Registrar compra para el proveedor? Antes de continuar, verifique las cantidades y subtotales!"
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
