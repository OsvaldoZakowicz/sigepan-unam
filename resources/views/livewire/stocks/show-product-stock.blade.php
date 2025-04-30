<div>
  {{-- componente listar stocks de un producto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de stock del producto {{ $product->product_name }}">
      <x-a-button
        wire:navigate
        href="{{ route('stocks-products-index') }}"
        class="mx-1"
        >volver
      </x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar stock</label>
            <input
              type="text"
              name="search_stock"
              wire:model.live="search_stock"
              wire:click="resetPagination()"
              placeholder="ingrese un id, lote, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
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
                lote
              </x-table-th>
              <x-table-th class="text-start">
                receta
              </x-table-th>
              <x-table-th class="text-end">
                cantidad elaborada
                <x-quest-icon title="total elaborado en el lote"/>
              </x-table-th>
              <x-table-th class="text-end">
                cantidad existente
                <x-quest-icon title="cantidad actual en el lote"/>
              </x-table-th>
              <x-table-th class="text-start">
                elaboración
                <x-quest-icon title="fecha de elaboracion de el lote"/>
              </x-table-th>
              <x-table-th class="text-start">
                vencimiento
                <x-quest-icon title="fecha de vencimiento del lote"/>
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($product_stocks as $stock)
              <tr wire:key="{{ $stock->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $stock->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $stock->lote_code }}
                </x-table-td>
                <x-table-td class="text-start">
                  receta
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $stock->quantity_total }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $stock->quantity_left }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $stock->elaborated_at->format('d-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $stock->expired_at->format('d-m-Y') }}
                  @if($stock->is_expired)
                    <span class="text-red-600">(Vencido)</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">

                  <x-a-button
                    href="#"
                    wire:click="openMovementsModal({{ $stock }})"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >movimientos
                  </x-a-button>

                  {{-- Modal de movimientos --}}
                  @if($show_movements_modal && $selected_stock)
                    <div class="fixed inset-0 bg-neutral-400 bg-opacity-20 overflow-y-auto h-full w-full flex items-center justify-center">
                      <div class="bg-white p-5 border rounded-md shadow-lg w-3/4 transform transition-all">
                        <div class="text-start">
                          <h3 class="text-lg leading-6 capitalize font-medium text-neutral-800">
                            Movimientos del Lote {{ $selected_stock->lote_code }}
                          </h3>

                          <div class="mt-4">
                            <x-table-base>
                              <x-slot:tablehead>
                                <tr class="border bg-neutral-100">
                                  <x-table-th class="text-end w-12">id</x-table-th>
                                  <x-table-th class="text-start">tipo</x-table-th>
                                  <x-table-th class="text-end">cantidad</x-table-th>
                                  <x-table-th class="text-start">fecha</x-table-th>
                                </tr>
                              </x-slot:tablehead>
                              <x-slot:tablebody>
                                @forelse ($selected_stock->stock_movements as $movement)
                                  <tr class="border">
                                    <x-table-td class="text-end">{{ $movement->id }}</x-table-td>
                                    <x-table-td class="text-start">{{ $movement->movement_type }}</x-table-td>
                                    <x-table-td class="text-end">{{ $movement->quantity }}</x-table-td>
                                    <x-table-td class="text-start">
                                      {{ $movement->registered_at->format('d-m-Y H:i') }}
                                    </x-table-td>
                                  </tr>
                                @empty
                                  <tr class="border">
                                    <td colspan="4" class="text-center">¡Sin movimientos registrados!</td>
                                  </tr>
                                @endforelse
                              </x-slot:tablebody>
                            </x-table-base>
                          </div>

                          <div class="flex justify-end gap-2 mt-6">
                            <x-btn-button
                              wire:click="$set('show_movements_modal', false)"
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
                <td colspan="8">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $product_stocks->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
