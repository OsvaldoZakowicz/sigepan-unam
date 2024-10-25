<div>
  {{-- componente crear rol --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de roles">
      <x-a-button wire:navigate href="{{ route('users-roles-create') }}" class="mx-1">crear rol</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar rol:</span>
        {{-- formulario de busqueda --}}
        <form class="flex gap-1 grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search" wire:click="resetPagination()" name="search" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
          {{-- es editable --}}
          <div class="w-fit flex items-center gap-1 border rounded-sm p-1 mx-1">
            <input type="checkbox" wire:model.live="editable" wire:click="resetPagination()" name="editable" id="editable" />
            <label for="editable">editables</label>
          </div>
          {{-- es interno? --}}
          <div class="w-fit flex items-center gap-1 border rounded-sm p-1 mx-1">
            <input type="checkbox" wire:model.live="internal" wire:click="resetPagination()" name="internal" id="internal" />
            <label for="internal">internos</label>
          </div>
        </form>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>
        <!-- tabla -->
        <table
          class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5">id</th>
              <th class="border text-left p-0.5">nombre</th>
              <th class="border text-left p-0.5">permisos</th>
              <th class="border text-left p-0.5">editable?</th>
              <th class="border text-left p-0.5">interno?</th>
              <th class="border text-left p-0.5">descripcion</th>
              <th class="border text-left p-0.5">fecha de creacion</th>
              <th class="border text-left p-0.5">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ($roles as $role)
            <tr wire:key="{{$role->id}}" class="border">
                <td class="border p-0.5">{{$role->id}}</td>
                <td class="border p-0.5">{{$role->name}}</td>
                <td class="border p-0.5">{{count($role->permissions)}}</td>
                <td class="border p-0.5">@if ($role->is_editable)<span>si</span>@else<span>no</span>@endif</td>
                <td class="border p-0.5">@if ($role->is_internal)<span>si</span>@else<span>no</span>@endif</td>
                <td class="border p-0.5">{{$role->short_description}}</td>
                <td class="border p-0.5">{{Date::parse($role->created_at)->format('d-m-Y');}}</td>
                <td class="border p-0.5">
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <a wire:navigate href="{{route('users-roles-edit', $role->id)}}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">editar</a>
                    {{-- boton delete con confirmacion --}}
                    <button wire:click="delete({{$role->id}})" wire:confirm="¿Desea borrar el registro?" type="button" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-red-600 bg-red-600 text-center text-neutral-100 uppercase text-xs">eliminar</button>
                  </div>
                </td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="8">sin registros!</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </x-slot:content>

      <x-slot:footer class="py-2">
        {{-- paginacion --}}
        {{ $roles->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
