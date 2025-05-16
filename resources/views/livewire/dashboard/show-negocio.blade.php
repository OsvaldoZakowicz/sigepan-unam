<div class="w-1/3">
  {{-- componente mostrar datos del negocio --}}
  <section class="p-2 border rounded-sm border-neutral-200 bg-neutral-50">
    <div class="p-4">
      <div class="flex justify-between items-center w-full gap-1">
        <h2 class="text-lg font-bold text-neutral-800 mb-2">Mi negocio</h2>
        @role('gerente')
          <x-a-button
            href="#"
            wire:click="abrirModal"
            >completar
          </x-a-button>
        @endrole
      </div>

      @if(count($datosNegocio) > 0)
        <div class="space-y-2">
          @foreach($datosNegocio as $dato)
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start border-b border-neutral-100 pb-1 text-sm">
              <span class="font-semibold min-w-40">{{ $dato['descripcion'] }}:</span>
              <span class="text-neutral-600">{{ $dato['valor'] }}</span>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-gray-500 italic">No hay datos del negocio registrados.</p>
      @endif
    </div>
  </section>

  {{-- Modal para completar datos del negocio --}}
  @if($mostrarModal)
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-md shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="p-4 border-b border-neutral-200">
        <h3 class="text-lg font-medium">Datos del negocio</h3>
      </div>

      <div class="p-4">
        <form wire:submit.prevent="guardarDatos">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Razón Social --}}
            <div>
              <label for="razon_social" class="text-sm font-semibold mb-1">Razón Social</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['razon_social'] }}" /></span>
              @error('form.razon_social')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="razon_social" wire:model="form.razon_social"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Nombre Comercial --}}
            <div>
              <label for="nombre_comercial" class="text-sm font-semibold mb-1">Nombre Comercial</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['nombre_comercial'] }}" /></span>
              @error('form.nombre_comercial')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="nombre_comercial" wire:model="form.nombre_comercial"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- CUIT --}}
            <div>
              <label for="cuit" class="text-sm font-semibold mb-1">CUIT</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['cuit'] }}" /></span>
              @error('form.cuit')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="cuit" wire:model="form.cuit"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Domicilio --}}
            <div>
              <label for="domicilio" class="text-sm font-semibold mb-1">Domicilio</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['domicilio'] }}" /></span>
              @error('form.domicilio')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="domicilio" wire:model="form.domicilio"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Condición IVA --}}
            <div>
              <label for="condicion_iva" class="text-sm font-semibold mb-1">Condición IVA</label>
              {{-- <span class="text-red-600" >*</span> --}}
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['condicion_iva'] }}" /></span>
              @error('form.condicion_iva')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="condicion_iva" wire:model="form.condicion_iva"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Ingresos Brutos --}}
            <div>
              <label for="ingresos_brutos" class="text-sm font-semibold mb-1">Ingresos Brutos</label>
              {{-- <span class="text-red-600" >*</span> --}}
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['ingresos_brutos'] }}" /></span>
              @error('form.ingresos_brutos')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="ingresos_brutos" wire:model="form.ingresos_brutos"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Inicio Actividades --}}
            <div>
              <label for="inicio_actividades" class="text-sm font-semibold mb-1">Inicio Actividades</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['inicio_actividades'] }}" /></span>
              @error('form.inicio_actividades')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="date" id="inicio_actividades" wire:model="form.inicio_actividades"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Punto de Venta --}}
            <div>
              <label for="punto_venta" class="text-sm font-semibold mb-1">Punto de Venta</label>
              {{-- <span class="text-red-600" >*</span> --}}
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['punto_venta'] }}" /></span>
              @error('form.punto_venta')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="punto_venta" wire:model="form.punto_venta"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Teléfono --}}
            <div>
              <label for="telefono" class="text-sm font-semibold mb-1">Teléfono</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['telefono'] }}" /></span>
              @error('form.telefono')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="telefono" wire:model="form.telefono"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Email --}}
            <div>
              <label for="email" class="text-sm font-semibold mb-1">Email</label>
              <span class="text-red-600" >*</span>
              <span class="text-sm text-center"><x-quest-icon title="{{ $descripciones['email'] }}" /></span>
              @error('form.email')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="email" id="email" wire:model="form.email"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-2">
            <x-a-button
              href="#" wire:click="cerrarModal"
              bg_color="neutral-600"
              border_color="neutral-600"
              text_color="neutral-100"
              >Cancelar
            </x-a-button>
            <x-btn-button
              >Guardar
            </x-btn-button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>
