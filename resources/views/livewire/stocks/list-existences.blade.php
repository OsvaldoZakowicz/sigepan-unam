<div>
  {{-- componente listar existencias --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de existencias: ingredientes e insumos">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar producto</label>
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
                ingrediente/insumo
              </x-table-th>
              <x-table-th class="text-start">
                cantidad disponible
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provision_categories as $key => $prov_categ)
              <tr wire:key="{{ $key }}" class="border">
                <x-table-td class="text-end">
                    {{ $prov_categ->id }}
                </x-table-td>
                <x-table-td class="text-start">
                    {{ $prov_categ->provision_category_name }}
                </x-table-td>
                <x-table-td class="text-start space-x-1">
                    {{-- {{ number_format($prov_categ->total_quantity, 2) }} --}}
                    {{ convert_measure($prov_categ->total_quantity, $prov_categ->measure()->first()) }}
                </x-table-td>
                <x-table-td class="text-start">

                  <div class="flex justify-start gap-1">
                    <x-a-button
                      wire:click="showDetails({{ $prov_categ->id }})"
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >detalles
                    </x-a-button>
                  </div>

                  {{-- modal de detalles --}}
                  @if($show_details_modal)
                    <div class="fixed inset-0 bg-neutral-400 bg-opacity-20 overflow-y-auto h-full w-full flex items-center justify-center">
                      <div class="bg-white p-5 border rounded-md shadow-lg w-3/4">
                        <!-- Encabezado del modal -->
                        <div class="flex justify-between items-center mb-4">
                          <h3 class="text-lg font-semibold text-neutral-800">
                            Detalles de {{ $selected_category->provision_category_name }}
                          </h3>
                          <button wire:click="closeDetails" class="text-neutral-500 hover:text-neutral-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                          </button>
                        </div>

                          <!-- Tabla de detalles -->
                        <div class="mt-4">
                          <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                              <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                  ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                  Marca
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                  Tipo Movimiento
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                  Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                  Cantidad
                                </th>
                              </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200">
                              @forelse($provision_details as $detail)
                                <tr>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                    {{ $detail->id }}
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">
                                    {{ $detail->trademark->provision_trademark_name }}
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                    {{ $detail->movement_type }}
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                    {{ \Carbon\Carbon::parse($detail->registered_at)->format('d/m/Y H:i') }}
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                    {{ convert_measure($detail->quantity_amount, $selected_category->measure) }}
                                  </td>
                                </tr>
                              @empty
                                <tr>
                                  <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-neutral-500">
                                    No hay movimientos registrados para esta categoría
                                  </td>
                                </tr>
                              @endforelse
                            </tbody>
                          </table>
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
        {{ $provision_categories->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
