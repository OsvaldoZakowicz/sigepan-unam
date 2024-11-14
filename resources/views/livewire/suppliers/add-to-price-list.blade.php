<div>
  {{-- componente alta de suministros en la lista de precios del proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="agregar suministros a la lista de precios del proveedor: {{ $supplier->company_name }}">
      <x-a-button wire:navigate href="{{ route('suppliers-suppliers-price-index', $supplier->id) }}" bg_color="neutral-100" border_color="neutral-300" text_color="neutral-600">volver</x-a-button>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="w-full flex-col">

        {{-- buscar suministros --}}
        <livewire:suppliers.search-provision />

        {{-- lista de suministros elegidos --}}
        <div>
          @forelse ($provisions as $pvs)
            <p>{{ $pvs->id }}, {{ $pvs->provision_name }}</p>
          @empty
            <p>vacio</p>
          @endforelse
        </div>

      </x-slot:content>

      <x-slot:footer class="hidden py-2">
        <!-- grupo de botones -->
        <div class="flex"></div>
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
