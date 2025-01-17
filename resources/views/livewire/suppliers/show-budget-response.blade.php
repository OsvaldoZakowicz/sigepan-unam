<div>
  {{-- componente ver respuestas de un presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section>

      <x-slot:title>
        <span>ver presupuesto,&nbsp;</span>
        <span>código del presupuesto:&nbsp;</span>
        <span class="font-semibold">{{ $quotation->quotation_code }}.&nbsp;</span>
        @if ($quotation->is_completed)
          <span>fecha del presupuesto:&nbsp;</span>
          <span class="font-semibold">{{ formatDateTime($quotation->updated_at, 'd-m-Y H:i:s') }}&nbsp;(último cambio)</span>
        @else
          <span class="font-semibold text-red-400">sin responder</span>
        @endif
      </x-slot:title>

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-budgets-periods-show', $quotation->period->id) }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver al periodo
      </x-a-button>

      {{-- todo: boton imprimir para obtener este presupuesto en pdf --}}

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- datos de cabecera del presupuesto --}}
        <div class="w-full flex flex-col justify-start items-start gap-1">
          {{-- proveedor --}}
          <span>
            <span class="font-semibold">proveedor:&nbsp;</span>
            <span>{{ $quotation->supplier->company_name }},&nbsp;</span>
            <span class="font-semibold">estado del proveedor:&nbsp;</span>
            @if ($quotation->supplier->status_is_active)
              <span
                class="font-semibold text-emerald-600 cursor-help"
                title="{{ $quotation->supplier->status_description }}"
                >activo
                <x-quest-icon/>
              </span>
            @else
              <span
                class="font-semibold text-neutral-600 cursor-help"
                title="{{ $quotation->quotation->supplier->status_description }}"
                >inactivo
                <x-quest-icon/>
              </span>
            @endif
          </span>
          <span>
            <span class="font-semibold">cuit:&nbsp;</span>
            <span>{{ $quotation->supplier->company_cuit }},&nbsp;</span>
            <span class="font-semibold">condición frente al iva:&nbsp;</span>
            <span>{{ $quotation->supplier->iva_condition }}</span>
          </span>
          {{-- direccion --}}
          <span>
            <span class="font-semibold">dirección:&nbsp;</span>
            <span>calle:&nbsp;{{ $quotation->supplier->address->street }},&nbsp;</span>
            <span>número:&nbsp;{{ $quotation->supplier->address->number }},&nbsp;</span>
            <span>ciudad:&nbsp;{{ $quotation->supplier->address->city }},&nbsp;</span>
            <span>código postal:&nbsp;{{ $quotation->supplier->address->postal_code }}</span>
          </span>
          {{-- contacto --}}
          <span>
            <span class="font-semibold">teléfono:&nbsp;</span>
            <span>{{ $quotation->supplier->phone_number }},&nbsp;</span>
            <span class="font-semibold">correo electrónico:&nbsp;</span>
            <span>{{ $quotation->supplier->user->email }}</span>
          </span>
        </div>

      </x-slot:header>

      <x-slot:content class="flex-col max-h-80 overflow-y-auto overflow-x-hidden">

        <span class="font-semibold my-2">suministros solicitados:</span>

        {{-- suministros presupuestados --}}
        {{-- todo: ver ejemplo --}}
        {{-- https://getquipu.com/wp-content/uploads/2023/01/03135440/plantilla-presupuesto-word-1-724x1024.png --}}

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id:</x-table-th>
              <x-table-th class="text-start">nombre</x-table-th>
              <x-table-th class="text-start">marca</x-table-th>
              <x-table-th class="text-end">volumen</x-table-th>
              <x-table-th class="text-start">cantidad</x-table-th>
              <x-table-th class="text-start w-1/6">tiene stock?</x-table-th>
              <x-table-th class="text-end w-1/4">$&nbsp;precio</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($provisions as $provision)
              <tr wire:key="{{ $provision->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $provision->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision->provision_name }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $provision->trademark->provision_trademark_name }}
                </x-table-td>
                <x-table-td class="text-end">
                  {{ $provision->provision_quantity }}&nbsp;{{ $provision->measure->measure_abrv }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{-- todo: agregar si es unidad o pack --}}
                  <span class="font-semibold">unidad/pack</span>
                </x-table-td>
                <x-table-td class="text-start">
                  @if ($quotation->is_completed)
                    @if ($provision->pivot->has_stock)
                      <span>si</span>
                    @else
                      <span>no</span>
                    @endif
                  @else
                    <span class="font-semibold text-red-400">-</span>
                  @endif
                </x-table-td>
                <x-table-td class="text-end">
                  @if ($quotation->is_completed)
                    <span>$&nbsp;{{ $provision->pivot->price }}</span>
                  @else
                    <span class="font-semibold text-red-400">-</span>
                  @endif
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="7">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="my-2">
        {{-- paginacion --}}
        {{ $provisions->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
