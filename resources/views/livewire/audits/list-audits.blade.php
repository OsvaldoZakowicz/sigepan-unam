<div>
  {{-- componente listar auditorias --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="lista de auditoria"></x-title-section>

    <x-content-section>

      <x-slot:header>

        {{-- campos de busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="">buscar id</label>
            <input
              type="text"
              name="search"
              id="search"
              wire:model.live="search"
              wire:click="resetPagination()"
              placeholder="ingrese un id"
              class="shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- evento --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="">filtrar por evento</label>
            <select
              wire:model.live="event"
              wire:click="resetPagination()"
              name="event"
              id="event"
              class="shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            >
              <option value="" selected>seleccione ...</option>
              <option value="created">creado</option>
              <option value="updated">actualizado</option>
              <option value="deleted">borrado</option>
            </select>
          </div>

          {{-- tabla --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="">filtrar por tabla</label>
            <select
              wire:model.live="table"
              wire:click="resetPagination()"
              name="table"
              id="table"
              class="shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="" selected>seleccione ...</option>
              {{-- iterar el array $tables, por cada $table generar una etiqueta option --}}
              @foreach ($tables as $table)
                <option value="{{ $table }}">{{ __( $table ) }}</option>
              @endforeach
            </select>
          </div>

          {{-- fecha de inicio --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_start_at">eventos desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- fecha de fin --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_end_at">eventos hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              wire:click="resetPagination()"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>
        </div>

        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>

      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">evento</x-table-th>
              <x-table-th class="text-start">tabla afectada</x-table-th>
              <x-table-th class="text-start">id del registro</x-table-th>
              <x-table-th
                class="text-start cursor-pointer"
                title="usuario o proveedor que generó el evento sobre el registro"
                >responsable&nbsp;<x-quest-icon/>
              </x-table-th>
              <x-table-th class="text-end">fecha del evento</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ( $audits as $audit )
            <tr wire:key="{{ $audit->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $audit->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ __( $audit->event ) }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ __( englishPluralFromPath($audit->auditable_type)->value ) }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $audit->auditable_id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $audit->user->name }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($audit->created_at)}}
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="w-fit-content inline-flex gap-2 justify-start items-center">

                    <x-a-button
                      wire:navigate
                      href="{{route('audits-audits-show', $audit->id)}}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral_600"
                      >ver cambios
                    </x-a-button>

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
