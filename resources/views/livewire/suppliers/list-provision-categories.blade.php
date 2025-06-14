<div>
  {{-- componente listar categorias de suministros --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de categorías de suministros">
      <x-a-button wire:navigate href="{{ route('suppliers-categories-create') }}" class="mx-1">crear categoria</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>

        {{-- busqueda --}}
        <div class="grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col">
            <label for="search_input">buscar categoría</label>
            <input
              type="text"
              name="search_input"
              wire:model.live="search_input"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o nombre ..."
              class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

        </div>

      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">categoría</x-table-th>
              <x-table-th class="text-start">tipo</x-table-th>
              <x-table-th class="text-start">unidad de medida</x-table-th>
              <x-table-th class="text-end">fecha de creación</x-table-th>
              <x-table-th class="text-start w-24">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($categories as $category)
              <tr wire:key="{{ $category->id }}" class="border">
                <x-table-td class="text-end">{{ $category->id }}</x-table-td>
                <x-table-td class="text-start">{{ $category->provision_category_name }}</x-table-td>
                <x-table-td class="text-start">{{ $category->provision_type->provision_type_name }}</x-table-td>
                <x-table-td class="text-start">{{ $category->measure->unit_name }} ({{ $category->measure->unit_symbol }})</x-table-td>
                <x-table-td class="text-end">{{ formatDateTime($category->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">

                    <x-a-button
                      wire:click="edit({{ $category->id }})"
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >editar
                    </x-a-button>

                    <x-btn-button
                      type="button"
                      wire:click="delete({{ $category->id }})"
                      wire:confirm="¿Desea borrar el registro?, eliminar una categoría hará que no este disponible para asignar a ningún suministro."
                      color="red"
                      >eliminar
                    </x-btn-button>

                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="6">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $categories->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
