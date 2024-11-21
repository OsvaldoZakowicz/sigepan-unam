<div>
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de permisos"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">permiso</x-table-th>
              <x-table-th class="text-start">interno?</x-table-th>
              <x-table-th class="text-start">descripcion</x-table-th>
              <x-table-th class="text-end">fecha de creacion</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($permissions as $permission)
            <tr wire:key="{{$permission->id}}" class="border">
                <x-table-td class="text-end">
                  {{$permission->id}}
                </x-table-td>
                <x-table-td class="text-start">
                  {{$permission->name}}
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($permission->is_internal) <span>si</span> @else <span>no</span> @endif
                </x-table-td>
                <x-table-td class="text-start">
                  {{$permission->short_description}}
                </x-table-td>
                <x-table-td class="text-end">
                  {{Date::parse($permission->created_at)->format('d-m-Y')}}
                </x-table-td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="5">sin registros!</td>
            </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">

        {{-- paginacion --}}
        {{ $permissions->links() }}

        <!-- grupo de botones -->
        <div class="flex"></div>

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
