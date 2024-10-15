<div>
  <article class="m-2 border rounded-sm border-neutral-200">
    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">lista de permisos</span>
      <div role="grupo-de-botones" class="flex"></div>
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
              <th class="border text-left p-0.5">permiso</th>
              <th class="border text-left p-0.5">interno?</th>
              <th class="border text-left p-0.5">descripcion</th>
              <th class="border text-left p-0.5">fecha de creacion</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ($permissions as $permission)
            <tr wire:key="{{$permission->id}}" class="border">
                <td class="border p-0.5">{{$permission->id}}</td>
                <td class="border p-0.5">{{$permission->name}}</td>
                <td class="border p-0.5">@if ($permission->is_internal) <span>si</span> @else <span>no</span> @endif</td>
                <td class="border p-0.5">{{$permission->short_description}}</td>
                <td class="border p-0.5">{{Date::parse($permission->created_at)->format('d-m-Y')}}</td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="4">sin registros!</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </section>
      <!-- seccion de pie -->
      <section class="flex items-center justify-end mt-2 px-1 py-2">
        {{-- paginacion --}}
        {{ $permissions->links() }}
        <!-- grupo de botones -->
        <div class="flex"></div>
      </section>
    </section>
  </article>
</div>
