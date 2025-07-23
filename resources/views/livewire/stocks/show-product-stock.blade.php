<div>
  {{-- componente listar stocks de un producto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de stock del producto: {{ $product->product_name }}">
      <x-a-button
        wire:navigate
        href="{{ route('stocks-products-index') }}"
        class="mx-1"
        bg_color="neutral-200"
        border_color="neutral-300"
        text_color="neutral-600"
        >volver
      </x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">
        {{-- busqueda --}}
        <div class="flex items-end justify-start gap-1">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-56">
            <label for="">buscar stock</label>
            <input
              type="text"
              name="search_stock"
              wire:model.live="search_stock"
              wire:click="resetPagination()"
              placeholder="ingrese un id o lote"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- ordenamiento --}}
          <div class="flex flex-col justify-end w-56">
            <label for="">ordenar lotes</label>
            <select
              name="order_stock"
              id="order_stock"
              wire:model.live="order_stock"
              wire:click="resetPagination()"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione...</option>
              <option value="expired_at">proximos vencimientos</option>
              <option value="elaborated_at">elaboraciones recientes</option>
            </select>
          </div>

          {{-- limpiar campos de busqueda --}}
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar filtros
          </x-a-button>
        </div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-12 text-end">
                id
              </x-table-th>
              <x-table-th class="text-start">
                lote
              </x-table-th>
              <x-table-th class="text-start">
                receta
              </x-table-th>
              <x-table-th class="text-end">
                elaborado
                <x-quest-icon title="total elaborado en el lote"/>
              </x-table-th>
              <x-table-th class="text-end">
                disponible
                <x-quest-icon title="cantidad actual en el lote"/>
              </x-table-th>
              <x-table-th class="text-start">
                elaboración
                <x-quest-icon title="fecha de elaboracion del lote"/>
              </x-table-th>
              <x-table-th class="text-start">
                vencimiento
                <x-quest-icon title="fecha de vencimiento del lote"/>
              </x-table-th>
              <x-table-th class="w-48 text-start">
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
                  <span class="text-xs uppercase">{{ $stock->lote_code }}</span>
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $stock->recipe->recipe_title }}
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
                    <x-text-tag color="red">vencido</x-text-tag>
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

                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="8">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- modal de movimientos del stock --}}
        @if($show_movements_modal && $selected_stock)
          <div class="fixed inset-0 flex items-center justify-center w-full h-full overflow-y-auto bg-neutral-400 bg-opacity-20">
            <div class="w-3/4 p-5 transition-all transform bg-white border rounded-md shadow-lg">
              <div class="text-start">
                {{-- cabecera del modal --}}
                <div>
                  <h3 class="text-lg font-medium leading-6 capitalize text-neutral-800">
                    Detalle de movimientos del Lote:
                  </h3>
                  <span>
                    <span class="font-semibold capitalize">Producto:</span>
                    <span>{{ $selected_stock->product->product_name }}</span>
                  </span>
                  <span>
                    <span class="font-semibold capitalize">Lote:</span>
                    <span class="text-xs uppercase">{{ $selected_stock->lote_code }}</span>
                  </span>
                  <span>
                    <span class="font-semibold capitalize">Cantidad elaborada:</span>
                    <span class="">{{ $selected_stock->quantity_total }}</span>
                  </span>
                  <span>
                    <span class="font-semibold capitalize">Cantidad disponible:</span>
                    <span class="">{{ $selected_stock->quantity_left }}</span>
                  </span>
                </div>
                {{-- cuerpo del modal --}}
                <div class="mt-4 overflow-x-auto overflow-y-auto max-h-72">
                  <x-table-base>
                    <x-slot:tablehead>
                      <tr class="border bg-neutral-100">
                        <x-table-th class="w-12 text-end">
                          id
                        </x-table-th>
                        <x-table-th class="text-start">
                          tipo
                          <x-quest-icon title="movimiento positivo (+) para elboracion y negativo (-) para ventas"/>
                        </x-table-th>
                        <x-table-th class="text-end">
                          cantidad
                        </x-table-th>
                        <x-table-th class="text-start">
                          fecha
                        </x-table-th>
                      </tr>
                    </x-slot:tablehead>
                    <x-slot:tablebody>
                      @forelse ($selected_stock->stock_movements()->orderBy('id', 'desc')->get() as $movement)
                        <tr class="border">
                          <x-table-td class="text-end">
                            {{ $movement->id }}
                          </x-table-td>
                          <x-table-td class="text-start">
                            @if (in_array($movement->movement_type, $positive_movements))
                              <span class="text-emerald-600">
                                &plus;{{ $movement->movement_type }}
                              </span>
                            @else
                              <span class="text-red-600">
                                &minus;{{ $movement->movement_type }}
                              </span>
                              @if ($movement->movement_type === 'venta')
                                <span class="text-neutral-600">(id de venta: {{ $movement->movement_reference_id }})</span>
                              @endif
                            @endif
                          </x-table-td>
                          <x-table-td class="text-end">
                            @if (in_array($movement->movement_type, $positive_movements))
                              <span class="text-emerald-600">
                                &plus;{{ $movement->quantity }}
                              </span>
                            @else
                              {{-- la cantidad ya es negativa --}}
                              <span class="text-red-600">
                                {{ $movement->quantity }}
                              </span>
                            @endif
                          </x-table-td>
                          <x-table-td class="text-start">
                            {{ $movement->registered_at->format('d-m-Y H:i') }} hs.
                          </x-table-td>
                        </tr>
                      @empty
                        <tr class="border">
                          <td colspan="4" class="text-start">¡Sin movimientos registrados!</td>
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

      </x-slot:content>

      <x-slot:footer class="justify-between py-2">
        {{-- total actual --}}
        <span class="font-semibold text-md">total disponible para venta:&nbsp;{{ $product->total_stock }}</span>
        {{ $product_stocks->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
