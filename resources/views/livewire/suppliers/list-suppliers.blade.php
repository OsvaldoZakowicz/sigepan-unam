<div>
  {{-- componente listar proveedores --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de proveedores">
      <x-a-button wire:navigate href="{{route('suppliers-suppliers-create')}}" class="mx-1">crear proveedor</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        <span class="text-sm capitalize">buscar proveedor:</span>
        {{-- formulario de busqueda --}}
        <form class="grow">
          {{-- termino de busqueda --}}
          <input type="text" name="search" placeholder="ingrese un id, o termino de busqueda" class="w-1/4 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
        </form>
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:header>

      <x-slot:content>
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5">id</th>
              <th class="border text-left p-0.5">razón social</th>
              <th class="border text-left p-0.5">cuit</th>
              <th class="border text-left p-0.5">teléfono</th>
              <th class="border text-left p-0.5">correo</th>
              <th class="border text-left p-0.5">fecha de creacion</th>
              <th class="border text-left p-0.5">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ($suppliers as $supplier)
              <tr wire:key="{{ $supplier->id }}" class="border">
                  <td class="border p-0.5">{{ $supplier->id }}</td>
                  <td class="border p-0.5">{{ $supplier->company_name }}</td>
                  <td class="border p-0.5">{{ $supplier->company_cuit }}</td>
                  <td class="border p-0.5">{{ $supplier->phone_number }}</td>
                  <td class="border p-0.5">{{ $supplier->user->email }}</td>
                  <td class="border p-0.5">{{ formatDateTime($supplier->created_at, 'd-m-Y') }}</td>
                  <td class="border p-0.5">
                    <div class="flex justify-start gap-1">
                      {{-- todo: ver proveedor --}}
                      <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver</x-a-button>
                      {{-- todo: editar proveedor --}}
                      <x-a-button wire:navigate href="#" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">editar</x-a-button>

                      <x-btn-button btn_type="button" color="red" wire:click="delete({{ $supplier->id }})" wire:confirm="¿desea borrar el registro? esta accion es irreversible, se eliminara el proveedor y sus credenciales de acceso">eliminar</x-btn-button>
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
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
