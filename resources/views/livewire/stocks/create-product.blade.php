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

              <div class="flex flex-col gap-4 w-1/2">
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
                <div class="flex gap-4">
                  {{-- precio --}}
                  <div class="flex flex-col gap-1 min-h-fit w-full">
                    <span>
                      <label for="product_price">$&nbsp;precio del producto</label>
                      <span class="text-red-600">*</span>
                    </span>
                    @error('product_price')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                    <input
                      name="product_price"
                      id="product_price"
                      wire:model="product_price"
                      type="text"
                      placeholder="$"
                      class="p-1 text-sm text-right border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                  </div>
                  {{-- vencimiento luego de elaboracion  --}}
                  <div class="flex flex-col gap-1 min-h-fit w-full">
                    <span>
                      <label for="product_expires_in">Días de vencimiento a partir de la elaboración</label>
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

              <div class="flex flex-col gap-4 w-1/2">
                {{-- publicar en tienda --}}
                <div class="flex flex-col gap-1 min-h-fit w-full">
                  <span>
                    <label for="product_in_store">publicar este producto en la tienda?</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('product_in_store')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  {{-- elegir etiquetas --}}
                  <select
                    name="product_in_store"
                    wire:model="product_in_store"
                    class="grow p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                    <option value="0">no publicar</option>
                    <option value="1">publicar</option>
                  </select>
                </div>
                {{-- decripcion corta --}}
                <div class="flex flex-col gap-1 min-h-fit w-full">
                  <span>
                    <label for="product_short_description">descripción corta del producto</label>
                    <span class="text-red-600">*</span>
                  </span>
                  @error('product_short_description')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
                  <textarea wire:model="product_short_description" name="product_short_description" rows="3" cols="10" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
                </div>
              </div>

            </div>

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
