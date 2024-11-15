<div>
  {{-- componente listar marcas --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de marcas de suministros">
      <x-a-button wire:navigate href="{{route('suppliers-trademarks-create')}}" class="mx-1">crear marca</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar marca:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search_input" name="search_input" wire:click="resetPagination" placeholder="ingrese un id, o nombre ..." class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
        </form>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>marca</x-table-th>
              <x-table-th>fecha de creación</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($trademarks as $trademark)
              <tr wire:key="{{ $trademark->id }}" class="border">
                <x-table-td>{{ $trademark->id }}</x-table-td>
                <x-table-td>{{ $trademark->provision_trademark_name }}</x-table-td>
                <x-table-td>{{ formatDateTime($trademark->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">
                    {{-- <x-a-button wire:navigate href="{{ route('trademarks-trademarks-show', $trademark->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver</x-a-button>
                    <x-a-button wire:navigate href="{{ route('trademarks-trademarks-edit', $trademark->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>
                    <x-btn-button btn_type="button" color="red" wire:click="delete({{ $trademark->id }})" wire:confirm="¿desea borrar el registro? esta accion es irreversible, se eliminara el proveedor y sus credenciales de acceso">eliminar</x-btn-button> --}}
                  </div>
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="4">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $trademarks->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>