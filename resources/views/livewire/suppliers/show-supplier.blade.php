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
        <table class="w-full table-auto border-collapse border rounded">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5 capitalize" colspan="2">proveedor:</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">id:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->id }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize ">razón social:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->company_name }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">cuit:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->company_cuit }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">condición frente al iva:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->iva_condition }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">telefono de contacto:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->phone_number }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">correo de contacto:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->user->email }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">direccion:</th>
              <td class="border p-0.5 lowercase">
                <span class="font-bold">calle:</span>
                <span>{{ $supplier->address->street }}</span>
                <span class="font-bold">numero de calle:</span>
                <span>{{ $supplier->address->number }}</span>
                <span class="font-bold">ciudad:</span>
                <span>{{ $supplier->address->city }}</span>
                <span class="font-bold">código postal:</span>
                <span>{{ $supplier->address->postal_code }}</span>
              </td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">fecha de creacion:</th>
              <td class="border p-0.5 lowercase">{{ formatDateTime($supplier->created_at, 'd-m-Y') }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">fecha de última edición:</th>
              <td class="border p-0.5 lowercase">{{ formatDateTime($supplier->updated_at, 'd-m-Y') }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">descripción:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->short_description }}</td>
            </tr>
          </tbody>
        </table>
        {{-- credenciales de acceso --}}
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5 capitalize" colspan="2">credenciales de acceso:</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">nombre de usuario:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->user->name }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">email:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->user->email }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">verificó el email?:</th>
              <td class="border p-0.5 lowercase">{{ $supplier->user->email_verified_at ? 'si' : 'no' }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">fecha de creacion:</th>
              <td class="border p-0.5 lowercase">{{ formatDateTime($supplier->user->created_at, 'd-m-Y') }}</td>
            </tr>
            <tr class="border">
              <th class="border text-left p-0.5 w-1/4 capitalize">fecha de última edición:</th>
              <td class="border p-0.5 lowercase">{{ formatDateTime($supplier->user->updated_at, 'd-m-Y') }}</td>
            </tr>
          </tbody>
        </table>
      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
