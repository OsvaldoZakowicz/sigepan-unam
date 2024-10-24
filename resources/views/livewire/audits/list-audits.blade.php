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
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5">id</th>
              <th class="border text-left p-0.5">evento</th>
              <th class="border text-left p-0.5 text-wrap">tabla</th>
              <th class="border text-left p-0.5 text-wrap">id registro</th>
              <th class="border text-left p-0.5 text-wrap">fecha del evento</th>
              <th class="border text-left p-0.5 text-wrap">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ( $audits as $audit )
            <tr wire:key="{{ $audit->id }}" class="border">
                <td class="border p-0.5">{{ $audit->id }}</td>
                <td class="border p-0.5">{{ __( $audit->event ) }}</td>
                <td class="border p-0.5 text-wrap lowercase">{{ __( englishPluralFromPath($audit->auditable_type)->value ) }}</td>
                <td class="border p-0.5 text-wrap">{{ $audit->auditable_id }}</td>
                <td class="border p-0.5 text-wrap">{{ formatDateTime($audit->created_at)}}</td>
                <td class="border p-0.5 text-wrap w-1/5">
                  <div class="w-fit-content inline-flex gap-2 justify-start items-center">

                    <x-a-button wire:navigate href="{{route('audits-audits-show', $audit->id)}}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral_600">auditar registro</x-a-button>

                    {{-- <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral_600">auditar tabla</x-a-button> --}}

                  </div>
                </td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="6">sin registros!</td>
            </tr>
            @endforelse
          </tbody>
        </table>
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
