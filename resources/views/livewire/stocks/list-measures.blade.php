<div>
  {{-- componente listar unidades de medida --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de unidades de medida">
      <x-a-button wire:navigate href="{{ route('stocks-measures-create') }}" class="mx-1">crear unidad</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar unidad:</span>
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
              <x-table-th>medida</x-table-th>
              <x-table-th>abreviación</x-table-th>
              <x-table-th>cantidad base</x-table-th>
              <x-table-th>descripcion corta</x-table-th>
              <x-table-th>es editable?</x-table-th>
              <x-table-th>fecha de creacion</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($measures as $measure)
            <tr wire:key="{{$measure->id}}" class="border">
                <x-table-td>{{ $measure->id }}</x-table-td>
                <x-table-td>{{ $measure->measure_name }}</x-table-td>
                <x-table-td>{{ $measure->measure_abrv }}</x-table-td>
                <x-table-td>{{ $measure->measure_base }}</x-table-td>
                <x-table-td>{{ $measure->measure_short_description }}</x-table-td>
                <x-table-td>{{ $measure->measure_is_editable ? 'si' : 'no' }}</x-table-td>
                <x-table-td>{{ formatDateTime($measure->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>
                    <x-btn-button type="button" color="red">eliminar</x-btn-button>
                  </div>
                </x-table-td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="8">sin registros!</td>
            </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{-- {{ $users->links() }} --}}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>
  </article>
</div>
