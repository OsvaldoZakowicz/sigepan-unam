<div>
  {{-- componente listar existencias --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de existencias por categoria: ingredientes e insumos">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar categoria</label>
            <input
              type="text"
              name="search_category"
              wire:model.live="search_category"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
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
                categoria
              </x-table-th>
              <x-table-th class="text-start">
                tipo
              </x-table-th>
              <x-table-th class="text-end">
                existencias totales
                <x-quest-icon title="kilogramos (kg), gramos (g), litros (L), mililitros (ml), metros (m), centimetros (cm)  o unidades (u)" />
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provision_categories as $provision_category)
              <tr wire:key="{{ $provision_category->id }}" class="border">
                <x-table-td class="text-end">
                    {{ $provision_category->id }}
                </x-table-td>
                <x-table-td class="text-start">
                    {{ $provision_category->provision_category_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision_category->provision_type()->first()->provision_type_name }}
                </x-table-td>
                <x-table-td class="text-end">
                    {{ convert_measure($provision_category->total_amount, $provision_category->measure()->first()) }}
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="flex justify-start gap-1">
                    <x-a-button
                      wire:click="showDetailsModal({{ $provision_category }})"
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >detalles
                    </x-a-button>
                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="8">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

        {{-- modal de detalles --}}
        @if($show_details_modal)
          <div class="fixed inset-0 z-50 bg-neutral-400 bg-opacity-20 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-5 border rounded-md shadow-lg w-3/4">
              <!-- encabezado del modal -->
              <div class="flex justify-between items-center mb-4">
                <div>
                  <h3 class="text-lg font-semibold text-neutral-800">
                    Detalle de movimientos sobre existencias:
                  </h3>
                  <span>
                    <span class="font-semibold">Categoria:</span>
                    <span>{{ $selected_category->provision_category_name }}</span>
                  </span>
                </div>
              </div>
              {{-- cuerpo del modal --}}
              <div class="mt-4 max-h-72 overflow-y-auto overflow-x-auto">
                <x-table-base>
                  <x-slot:tablehead>
                    <tr class="border bg-neutral-100">
                      <x-table-th class="text-end w-12">
                        id
                      </x-table-th>
                      <x-table-th class="text-start">
                        movimiento
                        <x-quest-icon title="movimiento positivo (+) para compras y negativo (-) en elaboración o pérdida" />
                      </x-table-th>
                      <x-table-th class="text-start">
                        suministro
                      </x-table-th>
                      <x-table-th class="text-end">
                        fecha de movimiento
                      </x-table-th>
                      <x-table-th class="text-end">
                        cantidad
                        <x-quest-icon title="kilogramos (kg), gramos (g), litros (L), mililitros (ml), metros (m), centimetros (cm)  o unidades (u)" />
                      </x-table-th>
                    </tr>
                  </x-slot:tablehead>
                  <x-slot:tablebody>
                    @forelse($provision_details as $detail)
                      <tr>
                        <x-table-td class="text-end">
                          {{ $detail->existence_id }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          @if ($detail->movement_type === $tipo_compra)
                            <span class="text-emerald-600">
                              &plus;{{ $detail->movement_type }}
                            </span>
                            <a
                              href="#"
                              wire:navigate
                              wire:click="goToPurchase({{ $detail->purchase_id }})"
                              class="text-blue-600 underline"
                              >ver
                            </a>
                            @else
                            <span class="text-red-600">
                              &minus;{{ $detail->movement_type }}
                            </span>
                            <a
                              href="#"
                              wire:navigate
                              wire:click="goToStock({{ $detail->stock_id }})"
                              class="text-blue-600 underline"
                              >ver
                            </a>
                          @endif
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $detail->provision_name }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{ \Carbon\Carbon::parse($detail->registered_at)->format('d/m/Y H:i') }} hs.
                        </x-table-td>
                        <x-table-td class="text-end">
                          @if ($detail->movement_type === $tipo_compra)
                            <span class="text-emerald-600">
                              &plus;{{ convert_measure($detail->quantity_amount, $selected_category->measure) }}
                            </span>
                          @else
                            <span class="text-red-600">
                              &minus;{{ convert_measure($detail->quantity_amount, $selected_category->measure) }}
                            </span>
                          @endif
                        </x-table-td>
                      </tr>
                      @if ($loop->last)
                        <tr>
                          <x-table-td colspan="4" class="text-end font-semibold capitalize">
                            Total:
                            <x-quest-icon title="sumatoria de todos los movimientos de la categoría" />
                          </x-table-td>
                          <x-table-td class="text-end font-semibold">
                            {{ convert_measure($total_amount, $selected_category->measure) }}
                          </x-table-td>
                        </tr>
                      @endif
                    @empty
                      <tr>
                        <x-table-td colspan="5" class="text-start">
                          No hay movimientos registrados para esta categoría
                        </x-table-td>
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
        @endif

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $provision_categories->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
