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
              class="w-96 border-2 border-dashed border-neutral-300"
            />
          </div>
          {{-- datos del producto --}}
          <div class="text-left">
            <h2 class="text-xl font-bold">
              {{ $product->product_name }}
            </h2>
            <p class="mt-2 text-md">
              <span class="font-semibold">Precio:</span>
              &nbsp;${{ $product->product_price }}
            </p>
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
            <div class="flex justify-start items-center gap-2 flex-wrap p-1 min-h-8 leading-none">
              @forelse ($product->tags as $key => $tag)
                <div class="flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 py-px px-1 rounded-lg">
                  <span class="text-sm text-neutral-600 lowercase">{{ $tag->tag_name }}</span>
                </div>
              @empty
                <span>¡no ha elegido ninguna etiqueta!</span>
              @endforelse
            </div>
            <p class="mt-2 text-md">
              <span class="font-semibold">Recetas:</span>
            </p>
            {{-- ver etiquetas --}}
            <div class="flex justify-start items-center gap-2 flex-wrap p-1 min-h-8 leading-none">
              @forelse ($product->recipes as $recipe)
                <div class="flex items-center justify-start gap-1 border border-blue-300 bg-blue-200 py-px px-1 rounded-lg">
                  <a
                    wire:navigate
                    href="{{ route('stocks-recipes-show', $recipe->id) }}"
                    class="text-sm cursor-pointer underline text-blue-500"
                    >{{ $recipe->recipe_title }}
                  </a>
                </div>
              @empty
                <span>¡este producto no tiene recetas!</span>
              @endforelse
            </div>
          </div>
        </div>
      </x-slot:content>

      <x-slot:footer class="mt-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
