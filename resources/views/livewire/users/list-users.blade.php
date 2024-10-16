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
                <td class="border p-0.5">{{$user->id}}</td>
                <td class="border p-0.5">{{$user->name}}</td>
                <td class="border p-0.5">{{$user->email}}</td>
                <td class="border p-0.5">{{$user->getRoleNames()->first();}}</td>
                <td class="border p-0.5">{{Date::parse($user->created_at)->format('d-m-Y');}}</td>
                <td class="border p-0.5">
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <a wire:navigate href="{{ route('users-users-edit', $user->id) }}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">editar</a>
                    {{-- boton delete con confirmacion --}}
                    <button wire:click="delete({{$user->id}})" wire:confirm="¿Desea borrar el registro?" type="button" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-red-600 bg-red-600 text-center text-neutral-100 uppercase text-xs">eliminar</button>
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
