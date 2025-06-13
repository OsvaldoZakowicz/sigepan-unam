<div>
  {{-- componente crear rol --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de roles">

      <x-a-button
        wire:navigate
        href="{{ route('users-roles-create') }}"
        class="mx-1"
        >crear rol
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- formulario de busqueda --}}
        <div class="w-full flex justify-start items-end gap-1">
          {{-- termino de busqueda --}}
          <div class="flex flex-col gap-1 w-1/4">
            <label>Buscar rol</label>
            <input
              type="text"
              name="search"
              wire:model.live="search"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- es editable --}}
          <div class="w-fit flex items-center gap-1 border rounded-sm p-1 mx-1">
            <input
              name="editable"
              id="editable"
              type="checkbox"
              wire:model.live="editable"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              />
            <label for="editable">editables</label>
          </div>

          {{-- es interno? --}}
          <div class="w-fit flex items-center gap-1 border rounded-sm p-1 mx-1">
            <input
              name="internal"
              id="internal"
              type="checkbox"
              wire:model.live="internal"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              />
            <label for="internal">internos</label>
          </div>

          {{-- limpiar filtros --}}
          <x-a-button
            href="#"
            wire:click='limpiar()'
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar filtros
          </x-a-button>

        </div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">permisos</x-table-th>
              <x-table-th class="text-start">editable?</x-table-th>
              <x-table-th class="text-start">interno?</x-table-th>
              <x-table-th class="text-start">descripcion</x-table-th>
              <x-table-th class="text-end">fecha de creacion</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($roles as $role)
            <tr wire:key="{{$role->id}}" class="border">
              <x-table-td class="text-end">
                {{$role->id}}
              </x-table-td>
              <x-table-td class="text-start">
                {{$role->name}}
              </x-table-td>
              <x-table-td class="text-start">
                {{count($role->permissions)}}
              </x-table-td>
              <x-table-td class="text-start">
                @if ($role->is_editable) <span>si</span> @else <span>no</span> @endif
              </x-table-td>
              <x-table-td class="text-start">
                @if ($role->is_internal) <span>si</span> @else <span>no</span> @endif
              </x-table-td>
              <x-table-td class="text-start">
                {{$role->short_description}}
              </x-table-td>
              <x-table-td class="text-end">
                {{Date::parse($role->created_at)->format('d-m-Y')}}
              </x-table-td>
              <x-table-td>
                <div class="w-full inline-flex gap-2 justify-start items-center">

                  <x-a-button
                    wire:navigate
                    href="{{route('users-roles-edit', $role->id)}}"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >editar
                  </x-a-button>

                  <x-btn-button
                    type="button"
                    color="red"
                    wire:click="delete({{$role->id}})"
                    wire:confirm="¿Desea borrar el registro?"
                    >eliminar
                  </x-btn-button>

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
        {{ $roles->links() }}

        <!-- grupo de botones -->
        <div class="flex"></div>

      </x-slot:footer>

    </x-content-section>

  </article>

  <script>
    // escuchar el evento reset-checkbox
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('reset-checkbox', () => {
        // obtener los checkboxes por su id
        const editableCheckbox = document.getElementById('editable');
        const internalCheckbox = document.getElementById('internal');

        // establecer checked = false
        if(editableCheckbox) editableCheckbox.checked = false;
        if(internalCheckbox) internalCheckbox.checked = false;
      });
    });
  </script>

</div>
