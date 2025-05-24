<div class="w-1/3">
  {{-- componente mostrar datos de la tienda --}}
  <section class="p-2 border rounded-sm border-neutral-200 bg-neutral-50">
    <div class="p-4">
      <div class="flex justify-between items-center w-full gap-1">
        <h2 class="text-lg font-bold text-neutral-800 mb-2">Mi tienda</h2>
        @role('gerente')
          <x-a-button
            href="#"
            wire:click="abrirModal"
            >completar
          </x-a-button>
        @endrole
      </div>

      @if(count($datosTienda) > 0)
        <div class="space-y-2">
          @foreach($datosTienda as $dato)
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start border-b border-neutral-100 pb-1 text-sm">
              <span class="font-semibold min-w-40">{{ $dato['descripcion'] }}:</span>
              <span class="text-neutral-600">{{ $dato['valor'] }}</span>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-gray-500 italic">No hay datos de la tienda registrados.</p>
      @endif
    </div>
  </section>

  {{-- Modal para completar datos de la tienda --}}
  @if($mostrarModal)
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-md shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="p-4 border-b border-neutral-200">
        <h3 class="text-lg font-medium">Datos de la tienda</h3>
      </div>

      <div class="p-4">
        <form wire:submit.prevent="guardarDatos">
          <div class="grid grid-cols-1 gap-4">
            {{-- Horario de Atención --}}
            <div>
              <label for="horario_atencion" class="text-sm font-semibold mb-1">Horario de Atención</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['horario_atencion'] }}" />
              </span>
              @error('form.horario_atencion')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="horario_atencion" wire:model="form.horario_atencion"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Lugar de Retiro --}}
            <div>
              <label for="lugar_retiro_productos" class="text-sm font-semibold mb-1">Lugar de Retiro</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['lugar_retiro_productos'] }}" />
              </span>
              @error('form.lugar_retiro_productos')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="lugar_retiro_productos" wire:model="form.lugar_retiro_productos"
                class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 w-full">
            </div>

            {{-- Tiempo de Espera --}}
            <div>
              <label for="tiempo_espera_pago" class="text-sm font-semibold mb-1">Tiempo de Espera para Pago</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['tiempo_espera_pago'] }}" />
              </span>
              @error('form.tiempo_espera_pago')
                <span class="block text-red-400 text-sm">{{ $message }}</span>
              @enderror
              <input type="text" id="tiempo_espera_pago" wire:model="form.tiempo_espera_pago"
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
