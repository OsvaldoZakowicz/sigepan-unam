<div>
  {{-- componente editar producto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="editar producto"></x-title-section>

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

              <div class="flex flex-col w-1/2 gap-4">
                <div class="flex gap-4">
                  {{-- nombre del producto --}}
                  <div class="flex flex-col w-full gap-1 min-h-fit">
                    <span>
                      <label for="product_name">nombre del producto</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_name')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                    @if (!$can_edit)
                      <span class="text-xs text-neutral-600">no puede cambiar el nombre del producto si tiene ventas, ordenes y/o stock asociado</span>
                    @endif
                    <input
                      name="product_name"
                      id="product_name"
                      wire:model="product_name"
                      type="text"
                      @readonly(!$can_edit)
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 @if (!$can_edit) bg-neutral-200 @endif"
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
                      <x-quest-icon title="indique si desea mostrar el producto en la tienda 'publicandolo' o no"/>
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
              <div class="flex flex-col w-1/2 gap-4">
                {{-- imagen del producto --}}
                <div class="flex flex-col w-full gap-1 min-h-fit">
                  <span>
                    <label for="new_product_image">imagen del producto</label>
                    <span class="text-red-600">*</span>
                  </span>
                  <span>puede seleccionar otro archivo para reemplazar la imagen existente</span>
                  @error('new_product_image')<span class="text-xs text-red-400">{{ $message }}</span>@enderror
                  <input
                    type="file"
                    wire:model="new_product_image"
                    id="new_product_image"
                    accept="image/*"
                    class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  />
                </div>
                {{-- vista previa de la imagen actual--}}
                <div class="flex flex-col w-full gap-1 min-h-fit">
                  @if ($new_product_image)
                    <img
                      src="{{ $new_product_image->temporaryUrl() }}"
                      alt="Imagen del producto no disponible"
                      class="max-w-xs border-2 border-dashed border-neutral-300"
                    />
                  @else
                    <img
                      src="{{ Storage::url($product->product_image_path) }}"
                      alt="Imagen del producto no disponible"
                      class="max-w-xs border-2 border-dashed border-neutral-300"
                    />
                  @endif
                </div>
              </div>

            </div>

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
            wire:confirm="¿editar producto?"
            >guardar
          </x-btn-button>

        </div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
