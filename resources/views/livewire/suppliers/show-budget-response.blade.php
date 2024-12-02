<div>
  {{-- componente ver respuestas de un presupuesto --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="ver presupuesto: {{ $quotation->quotation_code }}">

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-budgets-periods-show', $quotation->period->id) }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver al periodo</x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden">
      </x-slot:header>

      <x-slot:content class="flex-col">

        <span class="mb-1">
          <span class="font-semibold">presupuesto del proveedor:&nbsp;</span>
          <span>{{ $quotation->supplier->company_name }},&nbsp;</span>
          <span class="font-semibold">cuit:&nbsp;</span>
          <span>{{ $quotation->supplier->company_cuit }}</span>
        </span>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">id</x-table-th>
              <x-table-th class="text-start">suministro</x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($quotation->provisions as $provision)
              <tr wire:key="{{ $provision->id }}" class="border">
                <x-table-td class="text-end">
                  {{ $provision->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  <span class="font-semibold">suministro</span>:&nbsp;
                  {{ $provision->provision_name }},&nbsp;
                  <span class="font-semibold">marca</span>:&nbsp;
                  {{ $provision->trademark->provision_trademark_name }},&nbsp;
                  {{ $provision->provision_quantity }}&nbsp;({{ $provision->measure->measure_abrv }}),&nbsp;
                  <span class="font-semibold">cantidad: 1,&nbsp;</span>
                  <span class="font-semibold">precio indicado: $&nbsp;</span>
                  {{ $provision->pivot->price }},&nbsp;
                </x-table-td>
              </tr>
            @empty
              <tr class="border">
                <td colspan="2">sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="my-2">
        <div></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
