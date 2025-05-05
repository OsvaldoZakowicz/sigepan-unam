<div>
  {{-- componente listar compras --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de compras">
      <x-a-button
        wire:navigate
        href="{{ route('purchases-purchases-create') }}"
        class="mx-1"
        >registrar compra
      </x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar compra</label>
            <input
              type="text"
              name="search_purchase"
              wire:model.live="search_purchase"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- fecha de inicio --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_start_at">fecha de compra desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- fecha de fin --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_end_at">fecha de compra hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

        </div>

        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>

      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">
                id
              </x-table-th>
              <x-table-th class="text-start">
                proveedor
              </x-table-th>
              <x-table-th class="text-end">
                $costo
              </x-table-th>
              <x-table-th class="text-end">
                fecha de compra
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($purchases as $purchase)
              <tr wire:key="{{ $purchase->id }}">
                <x-table-td class="text-end">
                  {{ $purchase->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $purchase->supplier->company_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  ${{ number_format($purchase->total_price, 2) }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $purchase->purchase_date->format('d-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="flex gap-1">
                    <x-a-button
                      href="#"
                      wire:click="openDetailsModal({{ $purchase }})"
                      bg_color="neutral-100"
                      border_color="neutral-300"
                      text_color="neutral-600"
                      >detalle
                    </x-a-button>
                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="6">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- modal de detalle de compra --}}
        @if($show_details_modal && $selected_purchase)
          <div class="fixed z-50 inset-0 bg-neutral-400 bg-opacity-40 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-5 border rounded-md shadow-lg w-3/4 transform transition-all">
              <div class="text-start">
                <h3 class="text-lg leading-6 capitalize font-medium text-neutral-800">
                  Detalle de la compra <span class="uppercase">{{ $selected_purchase->id }}</span>
                </h3>
                <div class="flex flex-col">
                  <span><span class="font-semibold">Fecha de compra:</span> {{ $selected_purchase->purchase_date->format('d-m-Y') }}</span>
                  <span><span class="font-semibold">Proveedor:</span> {{ $selected_purchase->supplier->company_name }}, <span class="font-semibold">CUIT:</span> {{ $selected_purchase->supplier->company_cuit }}</span>
                  @php
                    $preorder_reference = $this->preorderReference($selected_purchase);
                  @endphp
                  @if ($preorder_reference)
                    <span><span class="font-semibold">Orden de compra:</span> {{ $preorder_reference['order_code'] }}, <span class="font-semibold">realizada el:</span> {{ $preorder_reference['order_date'] }}</span>
                    <a wire:navigate href="#" class="underline text-blue-500">ver preorden</a>
                  @else
                    <span>Compra registrada sin orden de compra previa</span>
                  @endif
                </div>
                <div class="mt-4">
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="text-end w-12">
                          Id
                        </x-table-th>
                        <x-table-th class="text-start">
                          Detalle
                        </x-table-th>
                        <x-table-th class="text-start">
                          Marca/Tipo/Volumen
                        </x-table-th>
                        <x-table-th class="text-end">
                          Cantidad comprada
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
                      @forelse ($selected_purchase->purchase_details as $detail)
                        <tr class="border">
                          <x-table-td class="text-end">
                            {{ $detail->id }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            @php
                              $detail_name = $detail->provision
                                ? $detail->provision->provision_name
                                : $detail->pack->pack_name
                            @endphp
                            {{ $detail_name }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            @php
                              $trademark = $detail->provision
                                ? $detail->provision->trademark->provision_trademark_name
                                : $detail->pack->provision->trademark->provision_trademark_name;

                              $type = $detail->provision
                                ? $detail->provision->type->provision_type_name
                                : $detail->pack->provision->type->provision_type_name;

                              $volume = $detail->provision
                                ? convert_measure($detail->provision->provision_quantity, $detail->provision->measure)
                                : convert_measure($detail->pack->provision->provision_quantity, $detail->pack->provision->measure);
                            @endphp
                            <span>{{ $trademark }}/</span>
                            <span>{{ $type }}/</span>
                            <span>{{ $volume }}</span>
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ $detail->item_count }}
                          </x-table-td>
                          <x-table-td class="text-end">
                            ${{ number_format($detail->unit_price, 2) }}
                          </x-table-td>
                          <x-table-td class="text-end">
                            ${{ number_format($detail->subtotal_price, 2) }}
                          </x-table-td>
                        </tr>
                        @if ($loop->last)
                          <tr class="border">
                            <x-table-td colspan="5" class="capitalize font-semibold text-end">$Total</x-table-td>
                            <x-table-td class="text-end font-semibold">${{ number_format($selected_purchase->total_price, 2) }}</x-table-td>
                          </tr>
                        @endif
                      @empty
                        <tr class="border">
                          <td colspan="6" class="text-start">¡Sin detalles registrados!</td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                  <x-btn-button
                    wire:click="closeDetailsModal()"
                    color="neutral"
                    >Cerrar
                  </x-btn-button>
                </div>
              </div>
            </div>
          </div>
        @endif

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $purchases->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
