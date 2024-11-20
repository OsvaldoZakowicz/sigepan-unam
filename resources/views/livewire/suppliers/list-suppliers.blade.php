<div>
  {{-- componente listar proveedores --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de proveedores">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-all') }}" class="mx-1" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">lista de precios general</x-a-button>
      <x-a-button wire:navigate href="{{route('suppliers-suppliers-create')}}" class="mx-1">crear proveedor</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar proveedor:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" wire:model.live="search_input" name="search" placeholder="ingrese un id, razon social, telefono o CUIT ..." class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
        </form>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th>id</x-table-th>
              <x-table-th>razón social</x-table-th>
              <x-table-th>cuit</x-table-th>
              <x-table-th>teléfono</x-table-th>
              <x-table-th>cond. iva</x-table-th>
              <x-table-th>estado</x-table-th>
              <x-table-th>fecha de creación</x-table-th>
              <x-table-th>acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($suppliers as $supplier)
              <tr wire:key="{{ $supplier->id }}" class="border">
                <x-table-td>{{ $supplier->id }}</x-table-td>
                <x-table-td>{{ $supplier->company_name }}</x-table-td>
                <x-table-td>{{ $supplier->company_cuit }}</x-table-td>
                <x-table-td>{{ $supplier->phone_number }}</x-table-td>
                <x-table-td>{{ $supplier->iva_condition }}</x-table-td>
                <x-table-td>
                  @if ($supplier->status_is_active)
                    <span class="font-semibold text-emerald-500">activo</span>
                  @else
                    <span class="font-semibold text-neutral-500">inactivo</span>
                  @endif
                </x-table-td>
                <x-table-td>{{ formatDateTime($supplier->created_at, 'd-m-Y') }}</x-table-td>
                <x-table-td>

                  <div class="flex justify-start gap-1">
                    <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">precios</x-a-button>
                    <x-a-button wire:navigate href="{{ route('suppliers-suppliers-show', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver</x-a-button>
                    <x-a-button wire:navigate href="{{ route('suppliers-suppliers-edit', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>
                    <x-btn-button btn_type="button" color="red" wire:click="delete({{ $supplier->id }})" wire:confirm="¿desea borrar el registro? esta accion es irreversible, se eliminara el proveedor y sus credenciales de acceso">eliminar</x-btn-button>
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
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
