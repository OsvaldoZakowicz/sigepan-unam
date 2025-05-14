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
        <form class="w-full flex flex-col gap-1">

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

            <div class="flex justify-start items-stretch gap-4">

              {{-- columna 1 --}}
              <div class="flex flex-col gap-4 w-1/2">
                <div class="flex gap-4">
                  {{-- nombre del producto --}}
                  <div class="flex flex-col gap-1 min-h-fit w-full">
                    <span>
                      <label for="product_name">nombre del producto</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_name')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
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
                  <div class="flex flex-col gap-1 min-h-fit w-full">
                    <span>
                      <label for="product_expires_in">Vencimiento despúes de elaborarse</label>
                      <x-quest-icon title="Una vez que se prepare este producto, ¿cuántos dias se mantiene fresco para la venta?"/>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_expires_in')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
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
                  <div class="flex flex-col gap-1 min-h-fit w-full">
                    <span>
                      <label for="product_in_store">publicar este producto en la tienda?</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_in_store')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
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
                <div class="flex flex-col gap-1 min-h-fit w-full">
                  <span>
                    <label for="product_short_description">descripción corta del producto</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('product_short_description')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <textarea
                    wire:model="product_short_description"
                    name="product_short_description"
                    rows="3"
                    cols="10"
                    class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                  </textarea>
                </div>
                {{-- etiquetas --}}
                <div class="flex flex-col gap-1 min-h-fit w-full">
                  <span>
                    <label for="">etiquetas de clasificacion</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('tags_list')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  {{-- elegir etiquetas --}}
                  <div class="flex justify-start items-center gap-1">
                    <select
                      name="selected_id_tag"
                      wire:model="selected_id_tag"
                      class="grow p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
                  <div class="flex justify-start items-center gap-2 flex-wrap p-1 min-h-8 border border-dashed border-neutral-200 leading-none overflow-y-auto overflow-x-auto">
                    @forelse ($tags_list as $key => $tag)
                      <div class="flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 py-px px-1 rounded-lg">
                        <span class="text-sm text-neutral-600 lowercase">{{ $tag['tag']->tag_name }}</span>
                        <span wire:click="removeTagFromList({{ $key }})" class="text-lg leading-none cursor-pointer text-red-400 hover:text-red-600 hover:font-semibold" title="">&times;</span>
                      </div>
                    @empty
                      <span>¡no ha elegido ninguna etiqueta!</span>
                    @endforelse
                  </div>
                </div>
              </div>

              {{-- columna 2 --}}
              <div class="flex flex-col gap-4 w-1/2">
                {{-- imagen del producto --}}
                <div class="flex flex-col gap-1 min-h-fit w-full">
                  <span>
                    <label for="product_image">imagen del producto</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('product_image')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <input
                    type="file"
                    wire:model="product_image"
                    id="product_image"
                    accept="image/*"
                    class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    >
                </div>
                {{-- vista previa de la imagen --}}
                @if ($product_image)
                  <div class="flex flex-col gap-1 min-h-fit w-full">
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
            <div class="flex gap-2 mb-2 items-end w-full">

              {{-- cantidad del producto --}}
              <div class="flex flex-col">
                <span>
                  <label for="quantity">Cantidad del producto</label>
                  <span class="text-red-600">*</span>
                </span>
                @error('quantity')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
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
                @error('price')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
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
                @error('price_description')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
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
                <label for="is_default">Precio predeterminado</label>
              </div>

              {{-- boton agregar --}}
              <button
                type="button"
                wire:click="addPrice"
                class="ml-auto px-4 py-1 bg-neutral-100 border border-neutral-300 text-neutral-600 rounded text-xs uppercase">
                Agregar precio
              </button>
            </div>

            {{-- lista de precios --}}
            <x-table-base class="w-full">
              <x-slot:tablehead>
                <tr class="bg-neutral-100 border">
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
                    Predeterminado
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
                        class="px-4 py-1 bg-red-100 border border-red-300 text-neutral-600 rounded text-xs uppercase">
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
        <div class="flex justify-end my-2 gap-2">

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
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
