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
          {{-- <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar compra</label>
            <input
              type="text"
              name="search_purchase"
              wire:model.live="search_purchase"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div> --}}

        </div>

        {{-- limpiar campos de busqueda --}}
        {{-- <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div> --}}

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
              {{-- <x-table-th class="text-start">
                estado
              </x-table-th> --}}
              <x-table-th class="text-end">
                $&nbsp;costo
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
                {{-- <x-table-td class="text-start">
                  {{ $purchase->status }}
                </x-table-td> --}}
                <x-table-td class="text-end">
                  {{ $purchase->total_price }}
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

                  {{-- modal de detalle de compra --}}
                  @if($show_details_modal && $selected_purchase)
                    <div class="fixed inset-0 bg-neutral-400 bg-opacity-20 overflow-y-auto h-full w-full flex items-center justify-center">
                      <div class="bg-white p-5 border rounded-md shadow-lg w-3/4 transform transition-all">
                        <div class="text-start">
                          <h3 class="text-md leading-6 capitalize font-medium text-neutral-800">
                            Detalle de la compra <span class="uppercase">{{ $purchase->id }}</span>
                          </h3>
                          <div class="flex flex-col">
                            <span>Fecha: {{ $purchase->purchase_date->format('d-m-Y') }}</span>
                            <span>Proveedor: {{ $purchase->supplier->company_name }}, CUIT: {{ $purchase->supplier->company_cuit }}</span>
                            {{-- todo: orden de compra relacionada --}}
                          </div>
                          <div class="mt-4">
                            <x-table-base>
                              <x-slot:tablehead>
                                <tr class="border bg-neutral-100">
                                  <x-table-th class="text-end w-12">
                                    id
                                  </x-table-th>
                                  <x-table-th class="text-start">
                                    detalle
                                  </x-table-th>
                                  <x-table-th class="text-end">
                                    cantidad
                                  </x-table-th>
                                  <x-table-th class="text-end">
                                    $precio unitario
                                  </x-table-th>
                                  <x-table-th class="text-end">
                                    $subtotal
                                  </x-table-th>
                                </tr>
                              </x-slot:tablehead>
                              <x-slot:tablebody>
                                @forelse ($purchase->purchase_details as $detail)
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
                                      <x-table-td colspan="4" class="capitalize font-semibold text-end">$Total</x-table-td>
                                      <x-table-td class="text-end">${{ number_format($purchase->total_price, 2) }}</x-table-td>
                                    </tr>
                                  @endif
                                @empty
                                  <tr class="border">
                                    <td colspan="5" class="text-center">¡Sin detalles registrados!</td>
                                  </tr>
                                @endforelse
                              </x-slot:tablebody>
                            </x-table-base>
                          </div>

                          <div class="flex justify-end gap-2 mt-6">
                            <x-btn-button
                              wire:click="$set('show_details_modal', false)"
                              color="neutral"
                              >Cerrar
                            </x-btn-button>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif

                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="6">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $purchases->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
