<div>
  {{-- componente listar proveedores --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de proveedores">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-suppliers-price-all') }}"
        class="mx-1"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >lista de precios general
      </x-a-button>

      <x-a-button
        wire:navigate
        href="{{route('suppliers-suppliers-create')}}"
        class="mx-1"
        >crear proveedor
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>

        {{-- busqueda --}}
        <div class="grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col">
            <span>buscar proveedor</span>
            <input
              type="text"
              wire:model.live="search_input"
              name="search"
              placeholder="ingrese razon social, telefono o CUIT ..."
              class="w-1/4 p-1 text-sm border shrink border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

        </div>

      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="w-12 text-end">id</x-table-th>
              <x-table-th class="text-start">razón social</x-table-th>
              <x-table-th class="text-end">cuit</x-table-th>
              <x-table-th class="text-end">teléfono</x-table-th>
              <x-table-th class="text-start">cond. iva</x-table-th>
              <x-table-th class="text-start">estado</x-table-th>
              <x-table-th class="text-end">fecha de creación</x-table-th>
              <x-table-th class="text-start w-60">acciones</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($suppliers as $supplier)
              <tr wire:key="{{ $supplier->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $supplier->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $supplier->company_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $supplier->company_cuit }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $supplier->phone_number }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $supplier->iva_condition }}
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($supplier->status_is_active)
                    <x-text-tag color="emerald">
                      activo
                      <x-quest-icon title="El proveedor esta activo para la panaderia, puede contactarse para presupuestos y ordenes de compras. Puede asignarle suministros y generarle una lista de precios"/>
                    </x-text-tag>
                  @else
                    <x-text-tag color="neutral">
                      inactivo
                      <x-quest-icon title="El proveedor no esta activo debido a {{ $supplier->status_description }}"/>
                    </x-text-tag>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  {{ formatDateTime($supplier->created_at, 'd-m-Y') }}
                </x-table-td>
                <x-table-td>
                  <div class="flex justify-start gap-1">

                    <x-a-button
                      wire:navigate
                      href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >precios
                    </x-a-button>

                    <x-a-button
                      wire:navigate
                      href="{{ route('suppliers-suppliers-show', $supplier->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver
                    </x-a-button>

                    @if ($supplier->deleted_at !== null)
                      <x-a-button
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        wire:click="restore({{ $supplier->id }})"
                        wire:confirm="¿restaurar proveedor?"
                        >restaurar
                      </x-a-button>
                    @else
                      <x-a-button
                        wire:navigate
                        href="{{ route('suppliers-suppliers-edit', $supplier->id) }}"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >editar
                      </x-a-button>

                      <x-btn-button
                        btn_type="button"
                        color="red"
                        wire:click="delete({{ $supplier->id }})"
                        wire:confirm="¿eliminar el proveedor?. Si no desea trabajar con un proveedor registrado, puede editarlo para desactivarlo. Los provedores eliminados no serán contactados para presupuestos y pre orenes de compra"
                        >eliminar
                      </x-btn-button>
                    @endif



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
