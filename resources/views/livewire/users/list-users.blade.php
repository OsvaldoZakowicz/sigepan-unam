<div>
  {{-- componente listar usuarios --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de usuarios">
      <x-a-button wire:navigate href="{{ route('users-users-create') }}" class="mx-1">crear usuario interno</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar usuario:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search" wire:click="resetPagination()" name="search" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
          {{-- roles --}}
          <select wire:model.live="role" wire:click="resetPagination()" name="role" id="role" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            <option value="" selected>filtrar por rol...</option>
            @foreach ($role_names as $role_name)
              <option value="{{$role_name}}">{{$role_name}}</option>
            @endforeach
          </select>
        </form>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>nombre de usuario</x-table-th>
              <x-table-th>correo</x-table-th>
              <x-table-th>rol</x-table-th>
              <x-table-th>fecha de creación</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($users as $user)
            <tr wire:key="{{$user->id}}" class="border">
                <x-table-td>{{ $user->id }}</x-table-td>
                <x-table-td>{{ $user->name }}</x-table-td>
                <x-table-td>{{ $user->email }}</x-table-td>
                <x-table-td>{{ $user->getRoleNames()->first(); }}</x-table-td>
                <x-table-td>{{ formatDateTime($user->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <x-a-button wire:navigate href="{{ route('users-users-edit', $user->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>
                    <x-btn-button type="button" wire:navigate wire:click="delete({{$user->id}})" wire:confirm="¿Desea borrar el registro?" color="red">eliminar</x-btn-button>
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
        {{ $users->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>
  </article>
</div>
