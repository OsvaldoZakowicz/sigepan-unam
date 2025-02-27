<div>
  {{-- componente responder pre orden --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="responder pre orden de compras">
      <x-slot:title>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">responder pre orden de compras</span>
      </x-slot:title>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="bg-blue-100 border-blue-500">
        <div class="flex flex-col p-2">
          <span class="font-semibold mb-2 text-base lg:text-sm text-neutral-800">¡aviso!</span>
          <span class="text-base lg:text-sm text-neutral-700">¡quedan ... dias para enviar una respuesta, hasta el cierre de este pedido de pre orden!</span>
        </div>
      </x-slot:header>

      <x-slot:content class="flex-col overflow-y-auto">

        {{-- una pre orden --}}
        <div class="bg-white rounded-md shadow-md overflow-hidden">
          <!-- Cabecera de la Pre-orden -->
          <div class="bg-neutral-100 p-4 border-b">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="text-lg text-neutral-800">
                  código: <span class="font-semibold uppercase">{{ $preorder->pre_order_code }}</span>
                </h3>
                <p class="text-lg text-neutral-600">
                  fecha: {{ formatDateTime($preorder->updated_at, 'd-m-Y') }}
                </p>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-neutral-800">
                  total: ${{ number_format($preorder->provisions->sum('pivot.total_price') + $preorder->packs->sum('pivot.total_price'), 2) }}
                </p>
                <p class="text-sm text-neutral-600">
                  ítems: {{ count($preorder->provisions) + count($preorder->packs) }}
                </p>
              </div>
            </div>
            <div class="mt-2 flex gap-2 justify-start items-center">
                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder->status }}
                  <x-quest-icon title="aún no completo su respuesta a esta pre orden"/>
                </x-text-tag>
                @if ($preorder->quotation_reference !== null)
                  <x-text-tag
                    color="neutral"
                    class="cursor-pointer"
                    >referencia: <span class="uppercase text-xs font-semibold">{{ $preorder->quotation_reference }}</span>
                    <x-quest-icon title="esta pre orden se creó a partir de uno de sus presupuestos"/>
                  </x-text-tag>
                  <x-a-button
                    wire:navigate
                    href="{{ route('quotations-quotations-show', $quotation->id) }}"
                    bg_color="neutral-100"
                    border_color="neutral-200"
                    text_color="neutral-600"
                    >ver presupuesto previo
                  </x-a-button>
                @endif
            </div>
          </div>

          <!-- Información del Proveedor y Emisor -->
          <div class="flex justify-start items-start gap-2 w-full border-b">
            {{-- emisor --}}
            <div class="p-4">
              <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Emisor</h4>
              <p class="font-medium">panaderia</p>
              <p class="text-sm text-neutral-600">CUIT: 99999999999</p>
              <p class="text-sm text-neutral-600">Contacto: email@ejemplo.com | Tel: 3758252525</p>
            </div>
            {{-- proveedor --}}
            <div class="p-4">
              <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Proveedor</h4>
              <p class="font-medium">{{ $preorder->supplier->company_name }}</p>
              <p class="text-sm text-neutral-600">CUIT: {{ $preorder->supplier->company_cuit }}</p>
              <p class="text-sm text-neutral-600">Contacto: {{ $preorder->supplier->user->email }} | Tel: {{ $preorder->supplier->phone_number }}</p>
            </div>
          </div>

          <!-- Suministros -->
          @if(count($preorder->provisions) > 0)
            <div class="p-4 border-b">
              <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">
                Suministros ({{ count($preorder->provisions) }})
              </h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200">
                  <thead class="bg-neutral-50">
                    <tr>
                      <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Suministro</th>
                      <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Marca/Tipo/volumen</th>
                      {{-- <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Tiene Stock</th> --}}
                      <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Cantidad</th>
                      <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Precio Unit.</th>
                      <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Total</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-neutral-200">
                    @foreach($preorder->provisions as $provision)
                      <tr>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                          {{ $provision->provision_name }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                          <span>
                            <span>{{ $provision->trademark->provision_trademark_name }} / {{ $provision->type->provision_type_name }}</span>
                            <span class="lowercase"> / de {{ convert_measure($provision->provision_quantity, $provision->measure) }}</span>
                          </span>
                        </td>
                        {{-- todo input check --}}
                        {{-- cuando la pre orden no se creo a partir de un presupuesto --}}
                        {{-- <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                        </td> --}}
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                          {{ $provision->pivot->quantity }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                          ${{ number_format($provision->pivot->unit_price, 2) }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                          ${{ number_format($provision->pivot->total_price, 2) }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot class="bg-neutral-100">
                    <tr>
                      <td colspan="4" class="px-3 py-2 text-sm font-medium text-neutral-800 text-right">Subtotal Suministros:</td>
                      <td class="px-3 py-2 text-sm font-bold text-neutral-800 text-right">
                        ${{ number_format($preorder->provisions->sum('pivot.total_price'), 2) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          @endif

          <!-- Packs -->
          @if(count($preorder->packs) > 0)
            <div class="p-4 border-b">
              <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">
                Packs ({{ count($preorder->packs) }})
              </h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack</th>
                      <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Tipo/Volumen</th>
                      {{-- <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tiene Stock</th> --}}
                      <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                      <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                      <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($preorder->packs as $pack)
                      <tr>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ $pack->pack_name }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                          <span>
                            <span>{{ $pack->provision->trademark->provision_trademark_name }} / {{ $pack->provision->type->provision_type_name }}</span>
                            <span class="lowercase"> / de {{ convert_measure($pack->pack_quantity, $pack->provision->measure) }}</span>
                          </span>
                        </td>
                        {{-- todo input check --}}
                        {{-- cuando la pre orden no se creo a partir de un presupuesto --}}
                        {{-- <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                        </td> --}}
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                          {{ $pack->pivot->quantity }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                          ${{ number_format($pack->pivot->unit_price, 2) }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                          ${{ number_format($pack->pivot->total_price, 2) }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot class="bg-gray-50">
                    <tr>
                      <td colspan="4" class="px-3 py-2 text-sm font-medium text-gray-900 text-right">Subtotal Packs:</td>
                      <td class="px-3 py-2 text-sm font-bold text-gray-900 text-right">
                        ${{ number_format($preorder->packs->sum('pivot.total_price'), 2) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          @endif

          <div class="p-4 bg-neutral-100">
            <div class="flex justify-between">

            {{-- formulario con otros datos --}}
            <div class="flex gap-2 justify-start items-center grow">
              {{-- retiro o envio --}}
              <div class="flex gap-2 justify-start items-center w-1/3">
                @error('delivery_type')<span>{{ $message }}</span> @enderror
                <div class="flex justify-start items-start gap-2 w-full">
                  <input
                    type="radio"
                    name="delivery_type"
                    wire:model.live="delivery_type"
                    value="delivery"
                    class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  />
                  <label>Trae a domicilio</label>
                </div>
                <div class="flex justify-start items-center gap-2 w-full">
                  <input
                    type="radio"
                    name="delivery_type"
                    wire:model.live="delivery_type"
                    value="pickup"
                    class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  />
                  <label>Retiro en el local</label>
                </div>
              </div>
              {{-- fecha de envio o retiro --}}
              <div class="flex flex-col gap-2 w-1/4">
                <label>Fecha de envío/retiro</label>
                @error('delivery_date') <span>{{ $message }}</span> @enderror
                <input
                  type="date"
                  wire:model="delivery_date"
                    min="{{ now()->format('Y-m-d') }}"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                />
              </div>
              {{-- metodo de pago --}}
              <div class="flex flex-col gap-2 w-1/4">
                <label>Método de pago</label>
                @error('payment_method') <span>{{ $message }}</span> @enderror
                <select
                  wire:model="payment_method"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                  >
                  <option value="">Seleccione método</option>
                  <option value="efectivo">Efectivo</option>
                  <option value="transferencia">Transferencia</option>
                  <option value="tarjeta">Tarjeta</option>
                  {{-- todo: metodos --}}
                </select>
              </div>
            </div>
            <!-- Resumen de Totales -->
            <div class="text-right space-y-1">
              <p class="text-sm font-medium text-neutral-500">
                subtotal suministros: ${{ number_format($preorder->provisions->sum('pivot.total_price'), 2) }}
              </p>
              <p class="text-sm font-medium text-neutral-500">
                subtotal packs: ${{ number_format($preorder->packs->sum('pivot.total_price'), 2) }}
              </p>
              <p class="text-lg font-bold text-neutral-800">
                total: ${{ number_format($preorder->provisions->sum('pivot.total_price') + $preorder->packs->sum('pivot.total_price'), 2) }}
              </p>
            </div>
          </div>
          {{-- seccion de botones --}}
          <div class="mt-4 flex justify-end space-x-3"></div>
        </div>
        </div>

      </x-slot:content>

      <x-slot:footer class="my-2">

        {{-- botones de guardado: desktop, display desde 1024px --}}
        <div class="hidden lg:flex w-full justify-end gap-2 mt-2">

          <x-a-button
            wire:navigate
            href="{{ route('quotations-quotations-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="submit"
            wire:confirm="¿confirma de ha leido toda la pre orden y puede cumplir con todos los suministros y/o packs solicitados?"
            >responder
          </x-btn-button>

        </div>

      </x-slot:footer>

    </x-content-section>

</article>
</div>
