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
                          <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @empty
                          <option value="">sin proveedores registrados.</option>
                        @endforelse
                      </select>
                    @endif

                  </div>
                  {{-- fecha de compra --}}
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
                <span class="text-sm text-neutral-600">suministros y/o packs adquiridos</span>
              @else
                <span class="text-sm text-neutral-600">complete los detalles de la compra indicando suministros y/o packs adquiridos</span>
              @endif
            </x-slot:subtitle>

            @error('purchase_items')
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
                        ">Total
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

              {{-- componente de busqueda de suministros y packs --}}
              {{-- tabla con vista de los resultados de busqueda e inputs dinamicos por fila --}}

            @endif

          </x-div-toggle>

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">
        <!-- botones del formulario -->
        <div class="flex justify-end my-2 gap-2">

          <x-a-button
            wire:navigate
            href="{{ route('purchases-preorders-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
