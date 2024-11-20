<div>
  {{-- componente listar recetas --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de recetas">
      <x-a-button wire:navigate href="{{route('stocks-recipes-create')}}" class="mx-1">crear receta</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar receta:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search" name="search" placeholder="ingrese un id, o término de busqueda ..." class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
        </form>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>nombre</x-table-th>
              <x-table-th>rendimiento</x-table-th>
              <x-table-th>fecha de creacion</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($recipes as $recipe)
              <tr wire:key="{{ $recipe->id }}" class="border">
                <x-table-td>{{ $recipe->id }}</x-table-td>
                <x-table-td>{{ $recipe->recipe_name }}</x-table-td>
                <x-table-td>{{ $recipe->recipe_production }}</x-table-td>
                <x-table-td>{{ formatDateTime($recipe->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">

                    <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">preparacion</x-a-button>

                    <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>

                    <x-btn-button btn_type="button" color="red" wire:click="" wire:confirm="¿desea borrar el registro? esta accion es irreversible, se eliminara la receta, ingredientes y preparación">eliminar</x-btn-button>

                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="5">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- todo paginacion --}}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
