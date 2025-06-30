<div>
  {{-- componente ver receta --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver receta">

      <x-a-button
        wire:navigate
        href="{{ route('stocks-recipes-index') }}"
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

        <div class="flex justify-start gap-4">

          {{-- producto --}}
          <div class="flex flex-col w-1/3 gap-4 p-2">
            {{-- datos del prodcuto --}}
            <div class="text-left">
              <h2 class="text-lg font-bold">
                <span class="font-semibold">Nombre:</span>
                {{ $recipe->recipe_title }}
                @if ($recipe->deleted_at)
                  <x-text-tag color="red">borrada</x-text-tag>
                @else
                  <x-text-tag color="emerald">activa</x-text-tag>
                @endif
              </h2>
              <p class="mt-2 text-md">
                <span class="font-semibold">Producto:</span>
                <a
                  wire:navigate
                  href="{{ route('stocks-products-show', $recipe->product->id) }}"
                  class="text-blue-500 underline cursor-pointer"
                  >{{ $recipe->product->product_name }}
                </a>
              </p>
              <p class="mt-2 text-md">
                <span class="font-semibold">Rendimiento:</span>
                &nbsp;{{ $recipe->recipe_yields }}&nbsp;unidades,&nbsp;con:&nbsp;{{ $recipe->recipe_portions }}&nbsp;porciones por unidad.
              </p>
              <p class="mt-2 text-md">
                <span class="font-semibold">tiempo de preparación:</span>
                  {{ \Carbon\Carbon::createFromFormat('H:i:s', $recipe->recipe_preparation_time)->format('G \h\o\r\a\s \y i \m\i\n\u\t\o\s') }}
              </p>
            </div>
            {{-- imagen --}}
            <div class="mr-4">
              <img
                src="{{ Storage::url($recipe->product->product_image_path) }}"
                alt="Imagen del producto"
                class="w-full border-2 border-dashed border-neutral-300"
              />
            </div>
          </div>

          {{-- ingredientes --}}
          <div class="flex flex-col w-1/3 gap-4 p-2">
            <div class="text-left">
              <h2 class="text-lg font-semibold">Ingredientes y suministros necesarios:</h2>
            </div>
            <div class="space-y-1">
              <div class="flex items-center justify-between mb-1">
                <span class="font-semibold">
                  Nombre
                </span>
                <span class="font-semibold">
                  Cantidad
                  <x-quest-icon title="kilogramos (kg), gramos (g), litros (L), mililitros (ml), metros (m), centimetros (cm)  o unidades (un)" />
                </span>
              </div>
              @foreach ($recipe->provision_categories as $category)
                <div class="flex items-center justify-between mb-1 border-b-2 border-dotted border-neutral-400">
                  <span>{{ $category->provision_category_name }}</span>
                  <span>{{ convert_measure($category->pivot->quantity, $category->measure) }}</span>
                </div>
              @endforeach
            </div>
          </div>

          {{-- instrucciones --}}
          <div class="flex flex-col w-1/3 gap-4 p-2">
            <div class="text-left">
              <h2 class="text-lg font-semibold">Instrucciones de preparación:</h2>
            </div>
            <div>
              <p>{{ $recipe->recipe_instructions }}</p>
            </div>
          </div>

        </div>
      </x-slot:content>

      <x-slot:footer class="mt-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
