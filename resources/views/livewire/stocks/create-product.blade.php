<div>
  {{-- componente crear producto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="crear producto"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        {{-- formulario del producto --}}
        <form class="flex flex-col w-full gap-1">

          <x-div-toggle x-data="{ open: true }" title="detalles del producto" class="w-full p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">complete los detalles principales del producto</span>
            </x-slot:subtitle>

            @error('recipe_*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            <div class="flex items-stretch justify-start gap-4">

              {{-- columna 1 --}}
              <div class="flex flex-col w-1/2 gap-4">
                <div class="flex gap-4">
                  {{-- nombre del producto --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit">
                    <span>
                      <label for="product_name">nombre del producto</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_name')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      name="product_name"
                      id="product_name"
                      wire:model="product_name"
                      type="text"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>
                </div>
                <div class="flex gap-4">
                  {{-- vencimiento luego de elaboracion  --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit">
                    <span>
                      <label for="product_expires_in">Vencimiento despúes de elaborarse</label>
                      <x-quest-icon title="Una vez que se prepare este producto, ¿cuántos dias se mantiene fresco para la venta?"/>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_expires_in')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <input
                      name="product_expires_in"
                      id="product_expires_in"
                      wire:model="product_expires_in"
                      type="number"
                      min="1"
                      class="p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>
                  {{-- publicar en tienda --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit">
                    <span>
                      <label for="product_in_store">publicar este producto en la tienda?</label>
                      <span class="text-red-600">*</span>
                      <x-quest-icon title="publicar hará que el producto aparezca en la tienda para el cliente"/>
                    </span>
                    @error('product_in_store')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    <select
                      name="product_in_store"
                      wire:model="product_in_store"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                      <option value="">seleccione una opcion ...</option>
                      <option value="1">publicar</option>
                      <option value="0">no publicar</option>
                    </select>
                  </div>
                </div>
                {{-- decripcion corta --}}
                <div class="flex flex-col w-full gap-1 min-h-fit">
                  <span>
                    <label for="product_short_description">descripción corta del producto</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('product_short_description')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                  <textarea
                    wire:model="product_short_description"
                    name="product_short_description"
                    rows="3"
                    cols="10"
                    class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                  </textarea>
                </div>
                {{-- etiquetas --}}
                <div class="flex flex-col w-full gap-1 min-h-fit">
                  <span>
                    <label for="">etiquetas de clasificacion</label>
                    <span class="text-red-600">*</span>
                    <x-quest-icon title="para una mejor descripcion y búsqueda del producto en la tienda"/>
                  </span>
                  @error('tags_list')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                  {{-- elegir etiquetas --}}
                  <div class="flex items-center justify-start gap-1">
                    <select
                      name="selected_id_tag"
                      wire:model="selected_id_tag"
                      class="p-1 text-sm border grow border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                      <option value="">seleccione ...</option>
                      @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
                      @endforeach
                    </select>
                    <x-a-button
                      href="#"
                      wire:click="addTagToList()"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >elegir
                    </x-a-button>
                  </div>
                  {{-- visualizar etiquetas --}}
                  <div class="flex flex-wrap items-center justify-start gap-2 p-1 overflow-x-auto overflow-y-auto leading-none border border-dashed min-h-8 border-neutral-200">
                    @forelse ($tags_list as $key => $tag)
                      <div class="flex items-center justify-start gap-1 px-1 py-px bg-blue-200 border border-blue-300 rounded-lg">
                        <span class="text-sm lowercase text-neutral-600">{{ $tag['tag']->tag_name }}</span>
                        <span wire:click="removeTagFromList({{ $key }})" class="text-lg leading-none text-red-400 cursor-pointer hover:text-red-600 hover:font-semibold" title="">&times;</span>
                      </div>
                    @empty
                      <span>¡no ha elegido ninguna etiqueta!</span>
                    @endforelse
                  </div>
                </div>
              </div>

              {{-- columna 2 --}}
              <div class="flex flex-col w-1/2 gap-4">
                {{-- imagen del producto --}}
                <div class="flex flex-col w-full gap-1 min-h-fit">
                  <span>
                    <label for="product_image">imagen del producto</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('product_image')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                  <input
                    type="file"
                    wire:model="product_image"
                    id="product_image"
                    accept="image/*"
                    class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    >
                </div>
                {{-- vista previa de la imagen --}}
                @if ($product_image)
                  <div class="flex flex-col w-full gap-1 min-h-fit">
                    <img src="{{ $product_image->temporaryUrl() }}" class="max-w-xs border-2 border-dashed border-neutral-300">
                  </div>
                @endif
              </div>

            </div>

          </x-div-toggle>

          <x-div-toggle x-data="{ open: true }" title="precios del producto" class="w-full p-2">

            {{-- leyenda --}}
            <x-slot:subtitle>
              <span class="text-sm text-neutral-600">establezca el/los precios del producto</span>
            </x-slot:subtitle>

            @error('prices_list')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡Debe agregar al menos un precio al producto!</span>
              </x-slot:messages>
            @enderror

            {{-- formulario para agregar precio --}}
            <div class="flex items-end w-full gap-2 mb-2">

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
                <x-quest-icon title="precio u oferta principal para el producto"/>
              </div>

              {{-- boton agregar --}}
              <button
                type="button"
                wire:click="addPrice"
                class="px-4 py-1 ml-auto text-xs uppercase border rounded bg-neutral-100 border-neutral-300 text-neutral-600">
                Agregar precio
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
                    Destacado
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
                        class="px-4 py-1 text-xs uppercase bg-red-100 border border-red-300 rounded text-neutral-600">
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

        </form>

      </x-slot:content>

      <x-slot:footer class="mt-2">
        <!-- botones del formulario -->
        <div class="flex justify-end gap-2 my-2">

          <x-a-button
            wire:navigate
            href="{{ route('stocks-products-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save()"
            wire:confirm="¿Crear producto?"
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
