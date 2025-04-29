<div>
  {{-- componente listar productos --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de productos">
      <x-a-button
        wire:navigate
        href="{{ route('stocks-products-create') }}"
        class="mx-1"
        >crear producto
      </x-a-button>
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
              name="search_product"
              wire:model.live="search_product"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- filtro de etiquetas --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="tag_filter">filtrar por etiquetas</label>
            <select
              name="tag_filter"
              id="tag_filter"
              wire:model.live="tag_filter"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione una etiqueta ...</option>
              @forelse ($tags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
              @empty
                <option value="">no hay etiquetas disponibles</option>
              @endforelse
            </select>
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
                producto
              </x-table-th>
              <x-table-th class="text-start">
                etiquetas
              </x-table-th>
              <x-table-th class="text-start">
                en stock
              </x-table-th>
              <x-table-th class="text-end">
                $&nbsp;precio
              </x-table-th>
              <x-table-th class="text-start">
                <span>publicado</span>
                <x-quest-icon title="indica si el producto aparece en la tienda para la venta"/>
              </x-table-th>
              <x-table-th class="text-start w-24">
                estado
              </x-table-th>
              <x-table-th class="text-start w-48">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($products as $product)
              <tr wire:key="{{ $product->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $product->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $product->product_name }}
                </x-table-td>
                <x-table-td class="text-start space-x-1">
                  @forelse ($product->tags as $tag)
                    @if ($loop->index < 2)
                      <span class="py-1 px-2 rounded-md text-xs lowercase text-neutral-600 bg-blue-200 border-blue-300">
                        {{ $tag->tag_name }}
                      </span>
                    @else
                      <span class="py-1 px-2 rounded-md text-xs lowercase text-neutral-600 bg-blue-200 border-blue-300">
                        +{{ $product->tags->count() }}
                      </span>
                    @endif
                  @empty
                    <span>ninguna</span>
                  @endforelse
                </x-table-td>
                <x-table-td class="text-end">
                  stock
                </x-table-td>
                <x-table-td class="text-end">
                  <span>$&nbsp;</span>
                  {{ $product->product_price }}
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($product->product_in_store)
                    <x-text-tag color="emerald">si</x-text-tag>
                  @else
                    <x-text-tag color="neutral">no</x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($product->deleted_at === 'borrado')
                    <x-text-tag color="neutral" >{{ $product->deleted_at }}</x-text-tag>
                  @else
                    <x-text-tag color="emerald">{{ $product->deleted_at }}</x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="flex justify-start gap-1">

                    @if ($product->deleted_at === 'activo')

                      <x-a-button
                        wire:click="openElaborationModal({{ $product->id }})"
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >elaborar
                      </x-a-button>

                      <x-a-button
                        wire:navigate
                        href="{{ route('stocks-products-show', $product->id) }}"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >ver
                      </x-a-button>

                      <x-a-button
                        wire:navigate
                        href="#"
                        wire:click="edit({{ $product->id }})"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >editar
                      </x-a-button>

                      <x-btn-button
                        btn_type="button"
                        color="red"
                        wire:click="delete({{ $product->id }})"
                        wire:confirm="¿Desea borrar el registro? El producto no estará disponible para elaborar o vender."
                        >eliminar
                      </x-btn-button>

                    @else

                      <x-a-button
                        wire:click="restore({{ $product->id }})"
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        title="revertir el borrado de este suministro."
                        wire:confirm="¿Desea restaurar el registro?, El producto volverá a estar disponible para elaborarse."
                        >restaurar
                      </x-a-button>

                    @endif

                  </div>

                  {{-- modal de elaboracion --}}
                  @if($show_elaboration_modal)
                  <div class="fixed inset-0 bg-neutral-400 bg-opacity-20 overflow-y-auto h-full w-full flex items-center justify-center" id="elaborationModal">
                    <div class="bg-white p-5 border rounded-md shadow-lg w-96 transform transition-all">
                      <div class="text-start">
                        <h3 class="text-lg leading-6 capitalize font-medium text-neutral-800">Elaborar Producto</h3>
                        <div class="mt-4">
                          <p class="text-sm text-start text-neutral-600">
                            Para elaborar <span class="font-semibold">{{ $selected_product->product_name }}</span>, elija una receta.
                          </p>
                          <div>
                            <select
                              wire:model="selected_recipe"
                              class="w-full mt-2 text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                              <option value="">Seleccione una receta...</option>
                              @forelse ($selected_product->recipes as $recipe)
                                <option value="{{ $recipe->id }}">{{ $recipe->recipe_title }}</option>
                              @empty
                                <option value="">El producto no tiene recetas</option>
                              @endforelse
                            </select>
                          </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-6">
                          <x-btn-button
                            wire:click="$set('show_elaboration_modal', false)"
                            color="neutral"
                            >Cancelar
                          </x-btn-button>
                          <x-btn-button
                            wire:click="elaborate()"
                            color="emerald"
                            >Confirmar
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
        {{ $products->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
