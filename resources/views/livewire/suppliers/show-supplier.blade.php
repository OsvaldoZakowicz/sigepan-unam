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
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border">
              <x-table-th colspan="2">proveedor</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-1/4">id</x-table-th>
              <x-table-td>{{ $supplier->id }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">razón social</x-table-th>
              <x-table-td>{{ $supplier->company_name }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">cuit</x-table-th>
              <x-table-td>{{ $supplier->iva_condition }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">teléfono de contacto</x-table-th>
              <x-table-td>{{ $supplier->phone_number }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">correo de contacto</x-table-th>
              <x-table-td>{{ $supplier->user->email }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">descripción</x-table-th>
              <x-table-td>{{ $supplier->short_description }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">dirección</x-table-th>
              <x-table-td>
                <span class="font-bold">calle:</span>
                <span>{{ $supplier->address->street }}</span>
                <span class="font-bold">numero de calle:</span>
                <span>{{ $supplier->address->number }}</span>
                <span class="font-bold">ciudad:</span>
                <span>{{ $supplier->address->city }}</span>
                <span class="font-bold">código postal:</span>
                <span>{{ $supplier->address->postal_code }}</span>
              </x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">fecha de creación</x-table-th>
              <x-table-td>{{ formatDateTime($supplier->created_at, 'd-m-Y') }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">fecha de última actualización</x-table-th>
              <x-table-td>{{ formatDateTime($supplier->updated_at, 'd-m-Y') }}</x-table-td>
            </tr>
          </x-slot:tablebody>
        </x-table-base>

        {{-- credenciales de acceso --}}
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th colspan="2">credenciales de acceso</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            <tr class="border">
              <x-table-th class="w-1/4">nombre de usuario</x-table-th>
              <x-table-td>{{ $supplier->user->name }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">correo electrónico</x-table-th>
              <x-table-td>{{ $supplier->user->email }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">verificación de email</x-table-th>
              <x-table-td>
                {{ $supplier->user->email_verified_at ? formatDateTime($supplier->user->email_verified_at, 'd-m-Y') : 'sin verificar' }}
              </x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">fecha de creación</x-table-th>
              <x-table-td>{{ formatDateTime($supplier->user->created_at, 'd-m-Y') }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">fecha de última actualización</x-table-th>
              <x-table-td>{{ formatDateTime($supplier->user->updated_at, 'd-m-Y') }}</x-table-td>
            </tr>
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
