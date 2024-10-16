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
              <th class="border text-left p-0.5">proveedor</th>
              <th class="border text-left p-0.5">razón social</th>
              <th class="border text-left p-0.5">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            {{-- @forelse (as)
            <tr wire:key="" class="border">
                <td class="border p-0.5"></td>
                <td class="border p-0.5"></td>
                <td class="border p-0.5"></td>
                <td class="border p-0.5"></td>
            </tr>
            @empty
            @endforelse --}}
            <tr class="border">
              <td colspan="4">sin registros!</td>
            </tr>
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
