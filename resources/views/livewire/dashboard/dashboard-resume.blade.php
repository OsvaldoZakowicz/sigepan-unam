<div class="w-full">
  {{-- card container --}}
  <main class="grid grid-cols-1 gap-8 p-8 sm:grid-cols-2">

    {{-- cards de informacion, visibles segun rol --}}
    {{-- Los componentes se posicionarán automáticamente en la cuadrícula --}}

    @role('gerente')
    <div class="w-full">
      {{-- resumen de periodos presupuestarios --}}
      @livewire('dashboard.show-quotation-period-status')
    </div>
    @endrole

    @role('gerente')
    <div class="w-full">
      {{-- resumen de periodos de preordenes --}}
      @livewire('dashboard.show-pre-order-period-status')
    </div>
    @endrole

    <div class="w-full">
      {{-- resumen de datos del negocio --}}
      @livewire('dashboard.show-negocio')
    </div>

    <div class="w-full">
      {{-- resumen de datos de tienda --}}
      @livewire('dashboard.show-tienda')
    </div>

  </main>
</div>