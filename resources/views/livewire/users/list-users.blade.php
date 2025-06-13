<div>
  {{-- componente listar usuarios --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de usuarios">

      <x-a-button
        wire:navigate
        href="{{ route('users-users-create') }}"
        class="mx-1"
        >crear usuario interno
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- formulario de busqueda --}}
        <div class="w-full flex justify-start gap-1 items-end">
          {{-- termino de busqueda --}}
          <div class="flex flex-col gap-1 w-1/4">
            <label>Buscar usuario</label>
            <input
              type="text"
              name="search"
              wire:model.live="search"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- roles --}}
          <div class="flex flex-col gap-1 w-1/4">
            <label>Filtrar por rol</label>
            <select
              wire:model.live="role"
              wire:click="resetPagination()"
              name="role"
              id="role"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="" selected>seleccione ...</option>
              @forelse ($role_names as $role_name)
                <option value="{{$role_name}}">{{$role_name}}</option>
              @empty
                <option value="">sin opciones ...</option>
              @endforelse
            </select>
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
              <x-table-th class="text-start">nombre de usuario</x-table-th>
              <x-table-th class="text-start">correo</x-table-th>
              <x-table-th class="text-start">rol</x-table-th>
              <x-table-th class="text-end">fecha de creación</x-table-th>
              <x-table-th class="text-start w-48">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($users as $user)
            <tr wire:key="{{$user->id}}" class="border">
                <x-table-td class="text-end">
                  {{ $user->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $user->name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $user->email }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $user->getRoleNames()->first() }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($user->created_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td class="text-start">
                  <div class="w-full inline-flex gap-1 justify-start items-center">

                    <x-a-button
                      wire:navigate
                      href="{{ route('users-users-edit', $user->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >editar
                    </x-a-button>

                    <x-btn-button
                      type="button"
                      wire:navigate
                      wire:click="delete({{$user->id}})"
                      wire:confirm="¿Desea borrar el registro?"
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
        {{ $users->links() }}

        <!-- grupo de botones -->
        <div class="flex"></div>

      </x-slot:footer>

    </x-content-section>
  </article>
</div>
