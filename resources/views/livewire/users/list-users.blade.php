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

        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5">id</th>
              <th class="border text-left p-0.5">nombre de usuario</th>
              <th class="border text-left p-0.5">email</th>
              <th class="border text-left p-0.5">rol</th>
              <th class="border text-left p-0.5">fecha de creacion</th>
              <th class="border text-left p-0.5">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ($users as $user)
            <tr wire:key="{{$user->id}}" class="border">
                <td class="border p-0.5">{{ $user->id }}</td>
                <td class="border p-0.5">{{ $user->name }}</td>
                <td class="border p-0.5">{{ $user->email }}</td>
                <td class="border p-0.5">{{ $user->getRoleNames()->first(); }}</td>
                <td class="border p-0.5">{{ formatDateTime($user->created_at, 'd-m-Y') }}</td>
                <td class="border p-0.5">

                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    @if ($user->getRoleNames()->first() !== $external_r and $user->getRoleNames()->first() !== $restricted_r and $user->id !== $current_user->id)

                      <x-a-button wire:navigate href="{{ route('users-users-edit', $user->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>

                      <x-btn-button type="button" wire:navigate wire:click="delete({{$user->id}})" wire:confirm="¿Desea borrar el registro?" color="red">eliminar</x-btn-button>

                    @else

                      <span title="el usuario no puede editarse o eliminarse si se trata de un proveedor, cliente o administrador en sesion actual" class="cursor-help">ninguna<span class="border rounded-full px-px my-px bg-neutral-100">&#65311;</span></span>

                    @endif
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
        {{ $users->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>
  </article>
</div>
