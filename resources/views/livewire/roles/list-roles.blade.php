<div>
  {{-- componente crear rol --}}
  <article class="m-2 border rounded-sm border-neutral-200">
    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">lista de roles</span>
      <div role="grupo-de-botones" class="flex">
        <a href="{{ route('users-roles-create') }}" class="flex justify-center items-center box-border w-fit h-6 m-1 p-1 border-solid border rounded border-blue-600 bg-blue-600 text-center text-neutral-100 uppercase text-xs">crear rol</a>
      </div>
    </section>
    <section class="flex flex-col pt-2 px-1 text-sm capitalize bg-white">
      <!-- seccion de cabecera -->
      {{-- <section class="flex items-center justify-between px-1 bg-neutral-200">
        <span class="text-sm capitalize">titulo de seccion</span>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </section> --}}
      <!-- seccion de contenido -->
      <section class="flex mt-2 px-1">
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
                  @if ($role->is_editable)
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <a wire:navigate href="{{route('users-roles-edit', $role->id)}}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">editar</a>
                    {{-- boton delete con confirmacion --}}
                    <button wire:click="delete({{$role->id}})" wire:confirm="¿Desea borrar el registro?" type="button" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-red-600 bg-red-600 text-center text-neutral-100 uppercase text-xs">eliminar</button>
                  </div>
                  @else
                  <span>ninguna</span>
                  @endif
                </td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="8">sin registros!</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </section>
      <!-- seccion de pie -->
      <section class="flex items-center justify-end mt-2 px-1 border-t">
        <!-- grupo de botones -->
        <div class="flex"></div>
      </section>
    </section>
  </article>
</div>
