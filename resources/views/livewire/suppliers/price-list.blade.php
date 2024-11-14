<div>
  {{-- componente lista de precios del proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de precios del proveedor: {{ $supplier->company_name }}">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-create', $supplier->id) }}">agregar suministros</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="w-full flex-col">

      </x-slot:content>

      <x-slot:footer class="hidden py-2">
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
