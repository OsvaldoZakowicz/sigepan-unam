<div>
  {{-- componente ver proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver proveedor">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-index') }}" bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">volver</x-a-button>
    </x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col gap-4 pb-2">

        {{-- proveedor --}}
        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th colspan="2">proveedor</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            <tr class="border">
              <x-table-th class="w-1/4">razón social</x-table-th>
              <x-table-td>
                <span class="capitalize font-semibold">{{ $supplier->company_name }}</span>,&nbsp;
                <span class="uppercase font-semibold">CUIT:</span>&nbsp;{{ $supplier->company_cuit }},&nbsp;
                <span class="uppercase font-semibold">IVA:</span>&nbsp;{{ $supplier->iva_condition }}
              </x-table-td>
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
              <x-table-th class="w-1/4">dirección</x-table-th>
              <x-table-td>
                <span class="capitalize font-semibold">calle:</span>
                <span>{{ $supplier->address->street }}</span>
                <span class="capitalize font-semibold">numero de calle:</span>
                <span>{{ $supplier->address->number }}</span>
                <span class="capitalize font-semibold">ciudad:</span>
                <span>{{ $supplier->address->city }}</span>
                <span class="capitalize font-semibold">código postal:</span>
                <span>{{ $supplier->address->postal_code }}</span>
              </x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">descripción general</x-table-th>
              <x-table-td>{{ $supplier->short_description }}</x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">fechas del registro</x-table-th>
              <x-table-td>
                <span class="capitalize font-semibold">creado:</span>&nbsp;{{ formatDateTime($supplier->created_at, 'd-m-Y') }},&nbsp;
                <span class="capitalize font-semibold">actualizado:</span>&nbsp;{{ formatDateTime($supplier->updated_at, 'd-m-Y') }},&nbsp;
              </x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">estado</x-table-th>
              <x-table-td>
                <span class="capitalize font-semibold">{{ ($supplier->status_is_active) ? 'activo' : 'inactivo' }}</span>,&nbsp;
                <span>desde:&nbsp;{{ formatDateTime($supplier->status_date, 'd-m-Y') }}</span>
              </x-table-td>
            </tr>
            <tr class="border">
              <x-table-th class="w-1/4">descripcion del estado</x-table-th>
              <x-table-td>{{ $supplier->status_description }}</x-table-td>
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
              <x-table-th class="w-1/4">fechas del registro</x-table-th>
              <x-table-td>
                <span class="capitalize font-semibold">creado:</span>&nbsp;{{ formatDateTime($supplier->user->created_at, 'd-m-Y') }},&nbsp;
                <span class="capitalize font-semibold">actualizado:</span>&nbsp;{{ formatDateTime($supplier->user->updated_at, 'd-m-Y') }},&nbsp;
              </x-table-td>
            </tr>
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>

  </article>
</div>
