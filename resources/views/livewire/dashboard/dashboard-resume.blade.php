<div class="w-full">
  {{-- card container --}}
  <main class="h-screen mb-4">
    <div class="flex flex-col items-center justify-center gap-2 p-2 mb-4 text-neutral-800">
      <span class="text-xl font-semibold">Bienvenido.</span>
      <span class="text-lg">SiGePAN - Sistema de Gestión para Panaderías.</span>
    </div>
    {{-- contenedor para cards de informacion segun roles --}}
    <div class="flex flex-wrap gap-4 p-4">

      @role('gerente')
      <div class="grow basis-1/3">
        @livewire('dashboard.show-quotation-period-status')
      </div>
      @endrole

      @role('gerente')
      <div class="grow basis-1/3">
        @livewire('dashboard.show-pre-order-period-status')
      </div>
      @endrole

      @role('gerente')
      <div class="grow basis-1/3">
        @livewire('dashboard.show-negocio')
      </div>
      @endrole

      @hasanyrole('gerente|vendedor')
      <div class="grow basis-1/3">
        @livewire('dashboard.show-tienda')
      </div>
      @endhasanyrole

    </div>
  </main>
  {{-- pie de seccion --}}
  <div class="flex items-center justify-center h-48 bg-neutral-900">
    <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
  </div>
</div>