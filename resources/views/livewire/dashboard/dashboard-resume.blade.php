<div class="w-full">
  {{-- card container --}}
  <main class="grid grid-cols-1 gap-8 p-8 sm:grid-cols-2">
    {{-- cards de informacion, visibles segun rol --}}
    {{-- Los componentes se posicionarán automáticamente en la cuadrícula --}}

    <div class="w-full">
      @livewire('dashboard.show-quotation-period-status')
    </div>

    <div class="w-full">
      @livewire('dashboard.show-pre-order-period-status')
    </div>


    <div class="w-full">
      @livewire('dashboard.show-negocio')
    </div>

    <div class="w-full">
      @livewire('dashboard.show-tienda')
    </div>

  </main>
</div>