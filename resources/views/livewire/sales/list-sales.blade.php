<div>
  {{-- componente listar ventas --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de ventas">

      <x-a-button
        href="#"
        wire:click="openNewSaleModal()"
        class="mx-1"
        >registrar ventas
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

          {{-- fecha de inicio --}}
          {{-- <div class="flex flex-col justify-end w-1/6">
            <label for="search_start_at">fecha de compra desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div> --}}

          {{-- fecha de fin --}}
          {{-- <div class="flex flex-col justify-end w-1/6">
            <label for="search_end_at">fecha de compra hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
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

        {{-- tabla de ventas realizadas --}}
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">
                id
              </x-table-th>
              <x-table-th class="text-start">
                tipo de venta
              </x-table-th>
              <x-table-th class="text-end">
                $total
              </x-table-th>
              <x-table-th class="text-end">
                fecha de venta
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($sales as $sale)
              <tr class="border bg-neutral-100">
                <x-table-td class="text-end">
                  {{ $sale->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $sale->payment_type }}
                </x-table-td>
                <x-table-td class="text-end">
                  ${{ number_format($sale->total_price, 2) }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $sale->created_at->format('d-m-Y H:i') }}
                </x-table-td>
                <x-table-td class="text-start">
                  -
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <x-table-td colspan="5">
                  <span>¡sin ventas ralizadas!</span>
                </x-table-td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- modal de registro de ventas --}}
        @if ($show_new_sale_modal)
          <div class="fixed z-50 inset-0 bg-neutral-400 bg-opacity-40 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-5 border rounded-md shadow-lg w-3/4 transform transition-all">
              <div class="text-start">

                {{-- titulo del modal --}}
                <h3 class="text-lg leading-6 capitalize font-medium text-neutral-800">
                  Nueva venta
                </h3>

                {{-- seccion de busqueda --}}
                <x-div-toggle
                  x-data="{ open: false }"
                  title="buscar productos"
                  subtitle="busque productos para agregarlos a la lista de ventas"
                  class="mt-4 p-2"
                  >
                  {{-- busqueda --}}
                  <div class="flex gap-1 justify-start items-start grow mb-1">
                    <div class="flex flex-col justify-end w-1/3">
                      <input
                        type="text"
                        name="search_product"
                        wire:model.live="search_product"
                        wire:click="resetPagination()"
                        placeholder="ingrese un id, o nombre de producto"
                        class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                    </div>
                  </div>
                  {{-- resultados de busqueda --}}
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="text-end w-12">
                          id
                        </x-table-th>
                        <x-table-th class="text-start">
                          producto
                        </x-table-th>
                        <x-table-th class="text-end">
                          $precio unitario
                        </x-table-th>
                        <x-table-th class="text-end">
                          cantidad disponible
                        </x-table-th>
                        <x-table-th class="text-end w-12">
                          elegir
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($available_products as $product)
                        <tr class="border">
                          <x-table-td class="text-end">
                            {{ $product->id }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{ $product->product_name }}
                          </x-table-td>
                          <x-table-td class="text-end">
                            ${{ number_format($product->product_price, 2) }}
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ $product->getTotalStockAttribute() }}
                          </x-table-td>
                          <x-table-td class="text-end w-12">
                            {{-- agregar a la lista --}}
                            <div class="w-full inline-flex gap-1 justify-start items-center">
                              <span
                                wire:click="addProductForSale({{ $product }})"
                                title="elegir y agregar a la lista"
                                class="font-bold leading-none text-center p-1 cursor-pointer bg-neutral-100 border border-neutral-200 rounded-sm"
                                >&plus;
                              </span>
                            </div>
                          </x-table-td>
                        </tr>
                      @empty
                        <tr class="border">
                          <x-table-td colspan="4" class="text-start">
                            <span>¡sin productos disponibles!</span>
                          </x-table-td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                  <div class="mt-1">
                    {{ $available_products->links() }}
                  </div>
                </x-div-toggle>

                {{-- seccion de lista de compras --}}
                <div class="mt-4">
                  @error('products_for_sale')
                    <span class="text-red-400 text-sm">{{ $message }}</span>
                  @enderror
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="text-end w-12">
                          Id
                        </x-table-th>
                        <x-table-th class="text-start">
                          Producto
                        </x-table-th>
                        <x-table-th class="text-end">
                          Cantidad a vender
                        </x-table-th>
                        <x-table-th class="text-end">
                          $Precio unitario
                        </x-table-th>
                        <x-table-th class="text-end">
                          $Subtotal
                        </x-table-th>
                        <x-table-th class="text-start w-12">
                          quitar
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($products_for_sale as $key => $pfs)
                        <tr wire:key="{{ $key }}" class="border">
                          <x-table-td class="text-end">
                            {{ $pfs['product']->id }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{ $pfs['product']->product_name }}
                          </x-table-td>
                          <x-table-td class="text-end w-56">
                            {{-- input cantidad a vender --}}
                            @error('products_for_sale.'.$key.'.sale_quantity')
                              <span class="text-red-400 text-xs">{{ $message }}</span>
                            @enderror
                            <div class="flex justify-end items-center">
                              <input
                                id="products_for_sale_{{ $key }}_sale_quantity"
                                wire:model.live="products_for_sale.{{ $key }}.sale_quantity"
                                type="number"
                                min="1"
                                max="{{ $pfs['product']->getTotalStockAttribute() }}"
                                class="p-1 w-full text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                              />
                            </div>
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ number_format($pfs['unit_price'], 2) }}
                          </x-table-td>
                          <x-table-td class="text-end">
                            {{ number_format($pfs['subtotal_price'], 2) }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{-- quitar de la lista --}}
                            <div class="w-full inline-flex gap-1 justify-start items-center">
                              <span
                                wire:click="removeProductForSale({{ $key }})"
                                title="elegir y agregar a la lista"
                                class="font-bold leading-none text-center p-1 cursor-pointer bg-red-100 border border-red-200 rounded-sm"
                                >&times;
                              </span>
                            </div>
                          </x-table-td>
                        </tr>
                      @empty
                        <tr class="border">
                          <x-table-td colspan="6">
                            <span>¡sin productos seleccionados!</span>
                          </x-table-td>
                        </tr>
                      @endforelse
                    </x-slot:tablebody>
                  </x-table-base>
                </div>

                {{-- total --}}
                <div class="flex justify-end gap-2 mt-2">
                  <span class="text-xl font-semibold capitalize">total:</span>
                  <span class="text-xl font-semibold">${{ number_format($total_for_sale, 2) }}</span>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                  <x-btn-button
                    color="neutral"
                    wire:click="closeNewSaleModal()"
                    >Cancelar
                  </x-btn-button>

                  <x-btn-button
                    wire:click="save()"
                    >Vender
                  </x-btn-button>
                </div>
              </div>
            </div>
          </div>
        @endif

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $sales->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
