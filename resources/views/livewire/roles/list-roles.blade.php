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

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>nombre</x-table-th>
              <x-table-th>permisos</x-table-th>
              <x-table-th>editable?</x-table-th>
              <x-table-th>interno?</x-table-th>
              <x-table-th>descripcion</x-table-th>
              <x-table-th>fecha de creacion</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($roles as $role)
            <tr wire:key="{{$role->id}}" class="border">
              <x-table-td>{{$role->id}}</x-table-td>
              <x-table-td>{{$role->name}}</x-table-td>
              <x-table-td>{{count($role->permissions)}}</x-table-td>
              <x-table-td>@if ($role->is_editable)<span>si</span>@else<span>no</span>@endif</x-table-td>
              <x-table-td>@if ($role->is_internal)<span>si</span>@else<span>no</span>@endif</x-table-td>
              <x-table-td>{{$role->short_description}}</x-table-td>
              <x-table-td>{{Date::parse($role->created_at)->format('d-m-Y');}}</x-table-td>
              <x-table-td>
                <div class="w-full inline-flex gap-2 justify-start items-center">
                  <a wire:navigate href="{{route('users-roles-edit', $role->id)}}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">editar</a>

                  <button wire:click="delete({{$role->id}})" wire:confirm="¿Desea borrar el registro?" type="button" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-red-600 bg-red-600 text-center text-neutral-100 uppercase text-xs">eliminar</button>
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
</div>
