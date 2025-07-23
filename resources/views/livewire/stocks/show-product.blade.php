<div>
  {{-- componente ver producto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver producto">

      <x-a-button
        wire:navigate
        href="{{ route('stocks-products-index') }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden">
      </x-slot:header>

      <x-slot:content class="flex-col">
        {{-- producto --}}
        <div class="flex">
          {{-- imagen --}}
          <div class="mr-4">
            <img
              src="{{ Storage::url($product->product_image_path) }}"
              alt="Imagen del producto"
              class="border-2 border-dashed w-96 border-neutral-300"
            />
          </div>
          {{-- datos del producto --}}
          <div class="text-left">
            <h2 class="text-xl font-bold">
              {{ $product->product_name }}
            </h2>
            @if ($product->getStatusAttribute() === 'borrado')
              <x-text-tag>borrado</x-text-tag>
            @endif
            <p class="mt-2 text-md">
              <span class="font-semibold">Precios en tienda:</span>
              <ul class="list-disc list-inside">
                @foreach ($product->prices as $price)
                  <li>
                    <span class="font-semibold">{{ $price->description }}</span>
                    <span>({{ $price->quantity }}) unidades, a $</span>
                    <span>{{ $price->price }}, </span>
                    @if ($price->is_default)
                      <x-text-tag color="emerald">destacado</x-text-tag>
                    @endif
                  </li>
                @endforeach   
              </ul>
            </p>
            {{-- si esta borrado no puedo editar precios --}}
            @if ($product->getStatusAttribute() === "activo")
              <p class="mt-2">
                <x-a-button
                  href="#"
                  wire:click='openEditPricesModal()'
                  bg_color="neutral-100"
                  border_color="neutral-200"
                  text_color="neutral-600"
                  >editar precios
                  <x-quest-icon title="los nuevos precios serán usados en las nuevas ordenes y ventas"/>
                </x-a-button>
              </p>
            @endif
            <p class="mt-2 text-md">
              <span class="font-semibold">Vencimiento después de elaborarse:</span>
              &nbsp;{{ $product->product_expires_in }}&nbsp;días.
            </p>
            <p class="mt-2 text-md">
              <span class="font-semibold">Publicado en la tienda?:</span>
              &nbsp;{{ ($product->product_in_store) ? 'si' : 'no' }}
            </p>
            <p class="mt-2 text-md">
              <span class="font-semibold">Descripción:</span>
              &nbsp;{{ $product->product_short_description }}
            </p>
            <p class="mt-2 text-md">
              <span class="font-semibold">Etiquetas de clasificacion:</span>
            </p>
            {{-- ver etiquetas --}}
            <div class="flex flex-wrap items-center justify-start gap-2 p-1 leading-none min-h-8">
              @forelse ($product->tags as $key => $tag)
                <div class="flex items-center justify-start gap-1 px-1 py-px bg-blue-200 border border-blue-300 rounded-lg">
                  <span class="text-sm lowercase text-neutral-600">{{ $tag->tag_name }}</span>
                </div>
              @empty
                <span>¡no ha elegido ninguna etiqueta!</span>
              @endforelse
            </div>
            <p class="mt-2 text-md">
              <span class="font-semibold">Recetas:</span>
            </p>
            {{-- ver recetas --}}
            <div class="flex flex-wrap items-center justify-start gap-2 p-1 leading-none min-h-8">
              @forelse ($product->recipes as $recipe)
                <div class="flex items-center justify-start gap-1 px-1 py-px bg-blue-200 border border-blue-300 rounded-lg">
                  <a
                    wire:navigate
                    href="{{ route('stocks-recipes-show', $recipe->id) }}"
                    class="text-sm text-blue-500 underline cursor-pointer"
                    >{{ $recipe->recipe_title }}
                  </a>
                </div>
              @empty
                <span>¡este producto no tiene recetas!</span>
              @endforelse
              {{-- si esta borrado no puedo agregar recetas --}}
              @if ($product->getStatusAttribute() === 'activo')
                <x-a-button
                  wire:navigate
                  href="{{ route('stocks-recipes-create') }}"
                  bg_color="neutral-100"
                  border_color="neutral-200"
                  text_color="neutral-600"
                  >agregar receta
                </x-a-button>
              @endif
            </div>
          </div>
        </div>
      </x-slot:content>

      <x-slot:footer class="mt-2">
      </x-slot:footer>

    </x-content-section>

    {{-- modal de edicion de precios --}}
      @if($show_edit_prices_modal)
      <form>
        <div class="fixed inset-0 flex items-center justify-center w-full h-full overflow-y-auto bg-neutral-400 bg-opacity-20" id="elaborationModal">
          <div class="max-w-5xl p-5 transition-all transform bg-white border rounded-md shadow-lg">
            <div class="text-start">
              <h3 class="text-lg font-medium leading-6 capitalize text-neutral-800">
                Editar precios del producto
              </h3>
              <x-div-toggle x-data="{ open: true }" title="precios del producto" class="w-full p-2 mt-4">

                {{-- leyenda --}}
                <x-slot:subtitle>
                  <span class="text-sm text-neutral-600">establezca el/los precios del producto</span>
                </x-slot:subtitle>

                @error('prices_list')
                  <x-slot:messages class="my-2">
                    <span class="text-red-400">{{ $message }}</span>
                  </x-slot:messages>
                @enderror

                {{-- formulario para agregar precio --}}
                <div class="flex items-end w-full gap-2 mb-2 text-sm">

                  {{-- cantidad del producto --}}
                  <div class="flex flex-col">
                    <span>
                      <label for="quantity">Cantidad del producto</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('quantity')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      type="number"
                      wire:model="quantity"
                      min="1"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                  </div>

                  {{-- precio --}}
                  <div class="flex flex-col">
                    <span>
                      <label for="price">Precio</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('price')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      type="number"
                      wire:model="price"
                      step="0.01"
                      min="0"
                      placeholder="0,00"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>

                  {{-- descripcion del precio --}}
                  <div class="flex flex-col w-1/3">
                    <span>
                      <label for="price_description">Descripción del precio</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('price_description')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      type="text"
                      wire:model="price_description"
                      placeholder="ej: 'unidad', 'media docena', 'docena', ..."
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>

                  {{-- precio predeterminado --}}
                  <div class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      wire:model="is_default"
                      id="is_default"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                    <label for="is_default">Precio destacado</label>
                  </div>

                  {{-- boton agregar --}}
                  <button
                    type="button"
                    wire:click="addPrice"
                    class="px-2 py-1 ml-auto text-xs uppercase border rounded bg-neutral-100 border-neutral-300 text-neutral-600">
                    Agregar
                  </button>
                </div>

                {{-- lista de precios --}}
                <x-table-base class="w-full">
                  <x-slot:tablehead>
                    <tr class="border bg-neutral-100">
                      <x-table-th class="text-start">
                        Cantidad
                      </x-table-th>
                      <x-table-th class="text-start">
                        $Precio
                      </x-table-th>
                      <x-table-th class="text-start">
                        Descripción
                      </x-table-th>
                      <x-table-th class="text-start">
                        destacado
                      </x-table-th>
                      <x-table-th class="text-start">
                        Acciones
                      </x-table-th>
                    </tr>
                  </x-slot:tablehead>
                  <x-slot:tablebody>
                    @forelse($prices_list as $index => $price)
                      <tr class="border">
                        <x-table-td class="">
                          {{ $price['quantity'] }}
                        </x-table-td>
                        <x-table-td class="">
                          ${{ number_format($price['price'], 2) }}
                        </x-table-td>
                        <x-table-td class="">
                          {{ $price['description'] }}
                        </x-table-td>
                        <x-table-td class="">
                          {{ $price['is_default'] ? 'Sí' : 'No' }}
                        </x-table-td>
                        <x-table-td class="">
                          <button
                            type="button"
                            wire:click="removePrice({{ $index }})"
                            class="px-2 py-1 text-xs uppercase bg-red-100 border border-red-300 rounded text-neutral-600">
                              Eliminar
                          </button>
                        </x-table-td>
                      </tr>
                    @empty
                      <tr>
                        <x-table-td colspan="5" class="text-start">
                          ¡No hay precios definidos!
                        </x-table-td>
                      </tr>
                    @endforelse
                  </x-slot:tablebody>
                </x-table-base>

              </x-div-toggle>
              <div class="flex justify-end gap-2 mt-6">
                <x-btn-button
                  wire:click="closeEditPricesModal()"
                  color="neutral"
                  >Cancelar
                </x-btn-button>
                <x-btn-button
                  wire:click="save()"
                  color="emerald"
                  >Confirmar
                </x-btn-button>
              </div>
            </div>
          </div>
        </div>
      </form>
      @endif

  </article>
</div>
