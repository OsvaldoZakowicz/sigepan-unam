<div>
  {{-- componente listar suministros --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de suministros">
      <x-a-button wire:navigate href="{{ route('suppliers-provisions-create') }}" class="mx-1">crear suministro</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar suministro:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" name="search" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
              <x-table-th>marca</x-table-th>
              <x-table-th>tipo</x-table-th>
              <x-table-th>cantidad, volumen o peso</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
            <tr wire:key="{{$provision->id}}" class="border">
                <x-table-td>{{ $provision->id }}</x-table-td>
                <x-table-td>{{ $provision->provision_name }}</x-table-td>
                <x-table-td>{{ $provision->trademark->provision_trademark_name }}</x-table-td>
                <x-table-td>{{ $provision->type->provision_type_name }}</x-table-td>
                <x-table-td>{{ $provision->provision_quantity }}&nbsp;({{ $provision->measure->measure_abrv }})</x-table-td>
                <x-table-td>
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>
                    <x-btn-button type="button" wire:navigate wire:confirm="¿Desea borrar el registro?" color="red">eliminar</x-btn-button>
                  </div>
                </x-table-td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="7">sin registros!</td>
            </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>
  </article>
</div>
