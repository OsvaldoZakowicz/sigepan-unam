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
              <th class="border text-left p-0.5">cantidad de permisos</th>
              <th class="border text-left p-0.5">editable?</th>
              <th class="border text-left p-0.5">interno?</th>
              <th class="border text-left p-0.5">descripcion</th>
              <th class="border text-left p-0.5">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ($roles as $role)
            <tr class="border">
                <td class="border p-0.5">{{$role->id}}</td>
                <td class="border p-0.5">{{$role->name}}</td>
                <td class="border p-0.5">{{count($role->permissions)}}</td>
                <td class="border p-0.5">{{$role->is_editable}}</td>
                <td class="border p-0.5">{{$role->is_internal}}</td>
                <td class="border p-0.5">{{$role->short_description}}</td>
                <td class="border p-0.5">acciones</td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="7">sin registros!</td>
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