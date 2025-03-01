<div>
  {{-- componente ver respuesta de pre orden --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="responder pre orden de compras">

      <x-slot:title>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">ver pre orden,&nbsp;</span>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">código de pre orden:&nbsp;</span>
        <span class="font-semibold uppercase text-sm">{{ $preorder->pre_order_code }}.</span>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">fecha de la pre orden:&nbsp;</span>
        <span class="font-semibold">{{ formatDateTime($preorder->updated_at, 'd-m-Y H:i:s') }} (último cambio).</span>
      </x-slot:title>

      <x-a-button
        wire:navigate
        href="{{ route('suppliers-preorders-show', $preorder->pre_order_period->id) }}"
        bg_color="neutral-100"
        border_color="neutral-200"
        text_color="neutral-600"
        >volver al periodo
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden">
      </x-slot:header>

      <x-slot:content class="flex-col overflow-y-auto">

        {{-- una pre orden --}}
        <div class="bg-white rounded-md shadow-md overflow-hidden">

          <!-- Cabecera de la Pre-orden -->
          <div class="bg-neutral-100 p-4 border-b">

            {{-- codigo y fecha --}}
            <div class="flex justify-between items-center">
              <div>
                <h3 class="text-lg text-neutral-800">
                  código: <span class="font-semibold uppercase">{{ $preorder->pre_order_code }}</span>
                </h3>
                <p class="text-lg text-neutral-600">
                  fecha: {{ formatDateTime($preorder->updated_at, 'd-m-Y') }}
                </p>
              </div>
            </div>

            {{-- estado y presupuesto de referencia --}}
            <div class="mt-2 flex gap-2 justify-start items-center">

              {{-- estado pendiente --}}
              @if ($preorder->status === $status_pending)
                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder->status }}
                  <x-quest-icon title="esta pre orden esta aún en tramite de aprobación"/>
                </x-text-tag>
              @endif

              {{-- estado aprobado --}}
              @if ($preorder->status === $status_approved)
                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder->status }}
                  <x-quest-icon title="tanto proveedor como la panaderia estan de acuerdo con esta pre orden de compra"/>
                </x-text-tag>
              @endif

              {{-- estado rechazado --}}
              @if ($preorder->status === $status_rejected)
                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder->status }}
                  <x-quest-icon title="una de las partes rechazó esta pre orden de compra"/>
                </x-text-tag>
              @endif

              {{-- si la pre orden se basa en un presupuesto previo --}}
              @if ($preorder->quotation_reference !== null)

                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >referencia: <span class="uppercase text-xs font-semibold">{{ $preorder->quotation_reference }}</span>
                </x-text-tag>

                <x-a-button
                  wire:navigate
                  href="{{ route('suppliers-budgets-response', $quotation->id) }}"
                  bg_color="neutral-100"
                  border_color="neutral-200"
                  text_color="neutral-600"
                  >ver presupuesto previo
                </x-a-button>

              @endif
            </div>

            {{-- detalle de fecha sobre la pre orden de referencia --}}
            @if ($preorder->quotation_reference !== null)
              <div class="mt-2 ">
                <span class="py-2 lowercase">esta pre orden se creó a partir del presupuesto obtenido el:&nbsp;</span>
                <span class="py-2 lowercase">{{ $quotation->updated_at->format('d-m-Y H:i:s') }}</span>
              </div>
            @endif

          </div>

          <!-- Información del Proveedor y Emisor -->
          <div class="flex justify-start items-start p-4 gap-2 w-full border-b">

            {{-- emisor --}}
            <div>
              <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Emisor</h4>
              <p class="font-medium">panaderia</p>
              <p class="text-sm text-neutral-600">CUIT: 99999999999</p>
              <p class="text-sm text-neutral-600">Contacto: email@ejemplo.com | Tel: 3758252525</p>
            </div>

            {{-- proveedor --}}
            <div>
              <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Proveedor</h4>
              <p class="font-medium">{{ $preorder->supplier->company_name }}</p>
              <p class="text-sm text-neutral-600">CUIT: {{ $preorder->supplier->company_cuit }}</p>
              <p class="text-sm text-neutral-600">Contacto: {{ $preorder->supplier->user->email }} | Tel: {{ $preorder->supplier->phone_number }}</p>
            </div>

          </div>

          {{-- items --}}
          <div class="p-4 border-b">
            <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Detalle:</h4>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"
                      >#
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"
                      >Suministro/Pack
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"
                      >Marca/Tipo/volumen
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                      ">Tiene Stock de la cantidad?
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                      ">Cantidad
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                      ">Precio Unit.
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                      ">Total
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                  @foreach($items as $key => $item)
                    @if ($item['item_type'] === $item_provision)
                      {{-- suministros --}}
                      <tr>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                          {{ $key+1 }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                          {{ $item['item_object']->provision_name }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                          <span>
                            <span>{{ $item['item_object']->trademark->provision_trademark_name }} / {{ $item['item_object']->type->provision_type_name }}</span>
                            <span class="lowercase"> / de {{ convert_measure($item['item_object']->provision_quantity, $item['item_object']->measure) }}</span>
                          </span>
                        </td>
                        {{-- input stock --}}
                        {{-- * PROVISIONS --}}
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-end text-neutral-500">
                          {{ ($item['item_has_stock']) ? 'si' : 'no' }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                          {{ $item['item_quantity'] }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                          ${{ number_format($item['item_unit_price'], 2) }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                          ${{ number_format($item['item_total_price'], 2) }}
                        </td>
                      </tr>
                    @else
                      {{-- packs --}}
                      <tr>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                          {{ $item['item_object']->pack_name }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                          <span>
                            <span>{{ $item['item_object']->provision->trademark->provision_trademark_name }} / {{ $item['item_object']->provision->type->provision_type_name }}</span>
                            <span class="lowercase"> / de {{ convert_measure($item['item_object']->provision->provision_quantity, $item['item_object']->provision->measure) }}</span>
                          </span>
                        </td>
                        {{-- input stock --}}
                        {{-- * PACKS --}}
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-end text-neutral-500">
                          {{ ($item['item_has_stock']) ? 'si' : 'no' }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                          {{ $item['item_quantity'] }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                          ${{ number_format($item['item_unit_price'], 2) }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                          ${{ number_format($item['item_total_price'], 2) }}
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
                <tfoot class="bg-neutral-100">
                  <tr>
                    <td colspan="6" class="px-3 py-2 text-normal font-medium text-neutral-800 text-right">Total:</td>
                    <td class="px-3 py-2 text-normal font-bold text-neutral-800 text-right">
                      $ {{ number_format($total_price, 2) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          {{-- detalle de envio, fecha, y medio de pago informado por el proveedor --}}
          <div class="flex gap-2 flex-wrap p-4 bg-neutral-100">

            <div class="flex flex-col">
              <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">anexo</h4>
              <span>El proveedor indica los siguientes datos para la pre orden:</span>
            </div>

            {{-- retiro o envio, fecha y método de pago en una línea --}}
            <div class="flex flex-wrap gap-4 w-full">
              {{-- retiro o envio --}}
              <div class="flex items-center">
                <span class="font-medium text-sm text-neutral-600">Tipo de entrega:</span>
                <span class="ml-2 text-sm">
                  @if(is_array($preorder->details['delivery_type']))
                    {{ implode(', ', $preorder->details['delivery_type']) }}
                  @else
                    {{ $preorder->details['delivery_type'] }}
                  @endif
                </span>
              </div>

              {{-- fecha de envio o retiro --}}
              <div class="flex items-center">
                <span class="font-medium text-sm text-neutral-600">Fecha de entrega:</span>
                <span class="ml-2 text-sm">
                  {{ \Carbon\Carbon::parse($preorder->details['delivery_date'])->format('d/m/Y') }}
                </span>
              </div>

              {{-- metodo de pago --}}
              <div class="flex items-center">
                <span class="font-medium text-sm text-neutral-600">Métodos de pago:</span>
                <span class="ml-2 text-sm">
                  @if(is_array($preorder->details['payment_method']))
                    {{ implode(', ', $preorder->details['payment_method']) }}
                  @else
                    {{ $preorder->details['payment_method'] }}
                  @endif
                </span>
              </div>
            </div>

            {{-- comentarios --}}
            @if($preorder->details['short_description'])
              <div class="w-full mt-2">
                <span class="font-medium text-sm text-neutral-600">Comentarios:</span>
                <p class="mt-1 text-sm text-neutral-700">
                  {{ $preorder->details['short_description'] }}
                </p>
              </div>
            @endif

          </div>

        </div>

      </x-slot:content>

      <x-slot:footer class="my-2">

        {{-- botones de guardado: desktop, display desde 1024px --}}
        <div class="hidden lg:flex w-full justify-end gap-2 mt-2">

          <x-a-button
            wire:navigate
            href="#"
            bg_color="red-600"
            border_color="red-600"
            wire:click=""
            wire:confirm="¿?"
            >rechazar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="approveAndMakeOrder()"
            wire:confirm="¿aprobar esta pre orden?, al aprobar la pre orden se emitirá una orden de compra final para todos los suministros y packs de la lista con stock"
            >aprobar y ordenar compra
          </x-btn-button>

        </div>

      </x-slot:footer>

    </x-content-section>

</article>
</div>
