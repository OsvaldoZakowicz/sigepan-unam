<div>
  {{-- componente listar etiquetas --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de etiquetas para productos">
      <x-a-button wire:navigate href="{{ route('stocks-tags-create') }}" class="mx-1">crear etiqueta</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>

        {{-- busqueda --}}
        <div class="grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col">
            <label for="search_input">buscar etiqueta</label>
            <input
              type="text"
              name="search_input"
              wire:model.live="search_input"
              wire:click="resetPagination"
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
              <x-table-th class="text-start">etiqueta</x-table-th>
              <x-table-th class="text-end">fecha de creación</x-table-th>
              <x-table-th class="text-start w-24">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($tags as $tag)
              <tr wire:key="{{ $tag->id }}" class="border">
                <x-table-td class="text-end">{{ $tag->id }}</x-table-td>
                <x-table-td class="text-start">{{ $tag->tag_name }}</x-table-td>
                <x-table-td class="text-end">{{ formatDateTime($tag->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">
                    <x-a-button
                      wire:click="edit({{ $tag->id }})"
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >editar
                    </x-a-button>

                    <x-btn-button
                      type="button"
                      wire:click="delete({{ $tag->id }})"
                      wire:confirm="¿Desea borrar el registro?, la etiqueta no estara disponible para usarse en productos"
                      color="red"
                      >eliminar
                    </x-btn-button>
                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="4">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $tags->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
