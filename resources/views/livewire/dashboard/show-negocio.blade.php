<div>
  {{-- componente mostrar datos del negocio --}}
  <section
    class="p-2 space-y-1 overflow-x-auto overflow-y-auto border rounded-md h-60 border-neutral-400 bg-neutral-50">
    <div class="p-4">
      <div class="flex items-center justify-between w-full gap-1 pb-2 mb-4 border-b border-neutral-200">
        <h2 class="text-lg font-bold text-neutral-800">Mi negocio</h2>
        @role('gerente')
        <x-a-button href="#" wire:click="abrirModal">completar
        </x-a-button>
        @endrole
      </div>

      @if(count($datosNegocio) > 0)
      <div class="space-y-1 overflow-y-auto max-h-36">
        @foreach($datosNegocio as $dato)
        <p class="pb-1 text-sm border-b border-neutral-100">
          <span class="font-semibold">{{ $dato['descripcion'] }}:</span>
          <span class="text-neutral-600">{{ $dato['valor'] }}</span>
        </p>
        @endforeach
      </div>
      @else
      <p class="italic text-gray-500">No hay datos del negocio registrados.</p>
      @endif
    </div>
  </section>

  {{-- Modal para completar datos del negocio --}}
  @if($mostrarModal)
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-md shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="p-4 border-b border-neutral-200">
        <h3 class="text-lg font-medium">Datos del negocio</h3>
      </div>

      <div class="p-4">
        <form wire:submit.prevent="guardarDatos">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Razón Social --}}
            <div>
              <label for="razon_social" class="mb-1 text-sm font-semibold">Razón Social</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['razon_social'] }}" />
              </span>
              @error('form.razon_social')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="razon_social" wire:model="form.razon_social"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Nombre Comercial --}}
            <div>
              <label for="nombre_comercial" class="mb-1 text-sm font-semibold">Nombre Comercial</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['nombre_comercial'] }}" />
              </span>
              @error('form.nombre_comercial')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="nombre_comercial" wire:model="form.nombre_comercial"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- CUIT --}}
            <div>
              <label for="cuit" class="mb-1 text-sm font-semibold">CUIT</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['cuit'] }}" />
              </span>
              @error('form.cuit')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="cuit" wire:model="form.cuit"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Domicilio --}}
            <div>
              <label for="domicilio" class="mb-1 text-sm font-semibold">Domicilio</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['domicilio'] }}" />
              </span>
              @error('form.domicilio')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="domicilio" wire:model="form.domicilio"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Condición IVA --}}
            <div>
              <label for="condicion_iva" class="mb-1 text-sm font-semibold">Condición IVA</label>
              {{-- <span class="text-red-600">*</span> --}}
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['condicion_iva'] }}" />
              </span>
              @error('form.condicion_iva')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="condicion_iva" wire:model="form.condicion_iva"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Ingresos Brutos --}}
            <div>
              <label for="ingresos_brutos" class="mb-1 text-sm font-semibold">Ingresos Brutos</label>
              {{-- <span class="text-red-600">*</span> --}}
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['ingresos_brutos'] }}" />
              </span>
              @error('form.ingresos_brutos')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="ingresos_brutos" wire:model="form.ingresos_brutos"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Inicio Actividades --}}
            <div>
              <label for="inicio_actividades" class="mb-1 text-sm font-semibold">Inicio Actividades</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['inicio_actividades'] }}" />
              </span>
              @error('form.inicio_actividades')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="date" id="inicio_actividades" wire:model="form.inicio_actividades"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Punto de Venta --}}
            <div>
              <label for="punto_venta" class="mb-1 text-sm font-semibold">Punto de Venta</label>
              {{-- <span class="text-red-600">*</span> --}}
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['punto_venta'] }}" />
              </span>
              @error('form.punto_venta')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="punto_venta" wire:model="form.punto_venta"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Teléfono --}}
            <div>
              <label for="telefono" class="mb-1 text-sm font-semibold">Teléfono</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['telefono'] }}" />
              </span>
              @error('form.telefono')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="telefono" wire:model="form.telefono"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Email --}}
            <div>
              <label for="email" class="mb-1 text-sm font-semibold">Email</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['email'] }}" />
              </span>
              @error('form.email')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="email" id="email" wire:model="form.email"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>
          </div>

          <div class="flex justify-end mt-6 space-x-2">
            <x-a-button href="#" wire:click="cerrarModal" bg_color="neutral-600" border_color="neutral-600"
              text_color="neutral-100">Cancelar
            </x-a-button>
            <x-btn-button>Guardar
            </x-btn-button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>