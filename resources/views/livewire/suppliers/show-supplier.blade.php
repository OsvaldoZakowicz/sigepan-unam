<div>
  {{-- componente ver proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver proveedor">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-index') }}" bg_color="neutral-600" border_color="neutral-600" text_color="neutral-100">volver</x-a-button>
    </x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col gap-4 pb-2">
        {{-- proveedor --}}
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5" colspan="2">proveedor:</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">id:</th>
              <td class="border p-0.5">{{ $supplier->id }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">razón social:</th>
              <td class="border p-0.5">{{ $supplier->company_name }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">cuit:</th>
              <td class="border p-0.5">{{ $supplier->company_cuit }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">condición frente al iva:</th>
              <td class="border p-0.5">{{ $supplier->iva_condition }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">telefono de contacto:</th>
              <td class="border p-0.5">{{ $supplier->phone_number }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">correo de contacto:</th>
              <td class="border p-0.5">{{ $supplier->user->email }}</td>
            </tr>
          </tbody>
        </table>
        {{-- direccion --}}
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5" colspan="2">direccion:</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">calle:</th>
              <td class="border p-0.5">{{ $supplier->address->street }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">número de calle:</th>
              <td class="border p-0.5">{{ $supplier->address->number }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">ciudad:</th>
              <td class="border p-0.5">{{ $supplier->address->city }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">código postal:</th>
              <td class="border p-0.5">{{ $supplier->address->postal_code }}</td>
            </tr>
          </tbody>
        </table>
        {{-- credenciales de acceso --}}
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5" colspan="2">credenciales de acceso:</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">nombre de usuario:</th>
              <td class="border p-0.5">{{ $supplier->user->name }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">email:</th>
              <td class="border p-0.5">{{ $supplier->user->email }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4">verificó el email?:</th>
              <td class="border p-0.5">{{ $supplier->user->email_verified_at ? 'si' : 'no' }}</td>
            </tr>
          </tbody>
        </table>
      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
