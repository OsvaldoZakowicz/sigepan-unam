<div>
  {{-- componente listar auditorias --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="lista de auditoria"></x-title-section>

    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar auditoria:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search" wire:click="resetPagination()" name="search" id="search" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
          {{-- evento --}}
          <select wire:model.live="event" wire:click="resetPagination()" name="event" id="event" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            <option value="" selected>filtrar por evento...</option>
            <option value="created">creado</option>
            <option value="updated">actualizado</option>
            <option value="deleted">borrado</option>
          </select>
        </form>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>evento</x-table-th>
              <x-table-th>tabla</x-table-th>
              <x-table-th>registro</x-table-th>
              <x-table-th>fecha del evento</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ( $audits as $audit )
            <tr wire:key="{{ $audit->id }}" class="border">
                <x-table-td>{{ $audit->id }}</x-table-td>
                <x-table-td>{{ __( $audit->event ) }}</x-table-td>
                <x-table-td>{{ __( englishPluralFromPath($audit->auditable_type)->value ) }}</x-table-td>
                <x-table-td>{{ $audit->auditable_id }}</x-table-td>
                <x-table-td>{{ formatDateTime($audit->created_at)}}</x-table-td>
                <x-table-td>
                  <div class="w-fit-content inline-flex gap-2 justify-start items-center">
                    <x-a-button wire:navigate href="{{route('audits-audits-show', $audit->id)}}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral_600">auditar registro</x-a-button>
                  </div>
                </x-table-td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="6">sin registros!</td>
            </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $audits->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
