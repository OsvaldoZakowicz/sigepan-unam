<div>
  {{-- componente mostrar datos de la tienda --}}
  <section
    class="p-2 space-y-1 overflow-x-auto overflow-y-auto border rounded-md h-60 border-neutral-400 bg-neutral-50">
    <div class="p-4">
      <div class="flex items-center justify-between w-full gap-1 pb-2 mb-4 border-b border-neutral-20">
        <h2 class="text-lg font-bold text-neutral-800">Mi tienda</h2>
        @hasanyrole('gerente|vendedor')
        <x-a-button href="#" wire:click="abrirModal">completar
        </x-a-button>
        @endhasanyrole
      </div>

      @if(count($datosTienda) > 0)
      <div class="space-y-1 overflow-y-auto max-h-36">
        @foreach($datosTienda as $dato)
        <p class="pb-1 text-sm border-b border-neutral-100">
          <span class="font-semibold">{{ $dato['descripcion'] }}:</span>
          <span class="text-neutral-600">{{ $dato['valor'] }}</span>
        </p>
        @endforeach
      </div>
      @else
      <p class="italic text-gray-500">No hay datos de la tienda registrados.</p>
      @endif
    </div>
  </section>

  {{-- Modal para completar datos de la tienda --}}
  @if($mostrarModal)
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-md shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="p-4 border-b border-neutral-200">
        <h3 class="text-lg font-medium">Datos de la tienda</h3>
      </div>

      <div class="p-4">
        <form wire:submit.prevent="guardarDatos">
          <div class="grid grid-cols-1 gap-4">
            {{-- Horario de Atención --}}
            <div>
              <label for="horario_atencion" class="mb-1 text-sm font-semibold">Horario de Atención</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['horario_atencion'] }}" />
              </span>
              @error('form.horario_atencion')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="horario_atencion" wire:model="form.horario_atencion"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Lugar de Retiro --}}
            <div>
              <label for="lugar_retiro_productos" class="mb-1 text-sm font-semibold">Lugar de Retiro</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['lugar_retiro_productos'] }}" />
              </span>
              @error('form.lugar_retiro_productos')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="lugar_retiro_productos" wire:model="form.lugar_retiro_productos"
                class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
            </div>

            {{-- Tiempo de Espera --}}
            <div>
              <label for="tiempo_espera_pago" class="mb-1 text-sm font-semibold">Tiempo de Espera para Pago</label>
              <span class="text-red-600">*</span>
              <span class="text-sm text-center">
                <x-quest-icon title="{{ $descripciones['tiempo_espera_pago'] }}" />
              </span>
              @error('form.tiempo_espera_pago')
              <span class="block text-sm text-red-400">{{ $message }}</span>
              @enderror
              <input type="text" id="tiempo_espera_pago" wire:model="form.tiempo_espera_pago"
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