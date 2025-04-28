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

            {{-- estado, evaluacion y presupuesto de referencia --}}
            <div class="mt-2 flex gap-2 justify-start items-center">

              {{-- estado de pre orden --}}
              @if ($preorder->is_completed)
                <x-text-tag
                  color="emerald"
                  class="cursor-pointer"
                  >respondido
                  <x-quest-icon title="el proveedor ha respondido"/>
                </x-text-tag>
              @else
                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >sin responder
                  <x-quest-icon title="el proveedor no ha respondido"/>
                </x-text-tag>
              @endif

              {{-- evaluacion pendiente --}}
              @if ($preorder->status === $status_pending)
                <x-text-tag
                  color="neutral"
                  class="cursor-pointer"
                  >{{ $preorder->status }}
                  <x-quest-icon title="esta pre orden esta aún en tramite de aprobación"/>
                </x-text-tag>
              @endif

              {{-- evaluacion aprobado --}}
              @if ($preorder->status === $status_approved)
                <x-text-tag
                  color="emerald"
                  class="cursor-pointer"
                  >{{ $preorder->status }}
                  <x-quest-icon title="tanto proveedor como la panaderia estan de acuerdo con esta pre orden de compra"/>
                </x-text-tag>
              @endif

              {{-- evaluacion rechazado --}}
              @if ($preorder->status === $status_rejected)
                <x-text-tag
                  color="red"
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

            {{-- detalle de fecha sobre el presupuesto de referencia --}}
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

            <x-div-toggle x-data="{ open: true }" title="suministros y packs de esta pre orden">
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
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-end text-neutral-500">
                            @if ($preorder->is_completed)
                              {{ ($item['item_has_stock']) ? 'si' : 'no' }}
                            @else
                              <span>sin respuesta</span>
                            @endif
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            @if(!$item['item_has_stock'])
                              <del class="text-neutral-400">{{ $item['item_quantity'] }}</del>
                              <span>cantidad alternativa:&nbsp;{{ $item['item_alternative_quantity'] }}</span>
                            @else
                              <span>{{ $item['item_quantity'] }}</span>
                            @endif
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>${{ number_format($item['item_unit_price'], 2) }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                            @if(!$item['item_has_stock'])
                              <del class="text-neutral-400">${{ number_format($item['item_total_price'], 2) }}</del>
                              <span>${{ number_format($item['item_alternative_quantity'] * $item['item_unit_price'], 2) }}</span>
                            @else
                              <span>${{ number_format($item['item_total_price'], 2) }}</span>
                            @endif
                          </td>
                        </tr>
                      @else
                        {{-- packs --}}
                        <tr>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                            {{ $key+1 }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800">
                            {{ $item['item_object']->pack_name }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                            <span>
                              <span>{{ $item['item_object']->provision->trademark->provision_trademark_name }} / {{ $item['item_object']->provision->type->provision_type_name }}</span>
                              <span class="lowercase"> / de {{ convert_measure($item['item_object']->pack_quantity, $item['item_object']->provision->measure) }}</span>
                            </span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-end text-neutral-500">
                            @if ($preorder->is_completed)
                              {{ ($item['item_has_stock']) ? 'si' : 'no' }}
                            @else
                              <span>sin respuesta</span>
                            @endif
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            @if(!$item['item_has_stock'])
                              <del class="text-neutral-400">{{ $item['item_quantity'] }}</del>
                              <span>cantidad alternativa:&nbsp;{{ $item['item_alternative_quantity'] }}</span>
                            @else
                              <span>{{ $item['item_quantity'] }}</span>
                            @endif
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            <span>${{ number_format($item['item_unit_price'], 2) }}</span>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                            @if(!$item['item_has_stock'])
                              <del class="text-neutral-400">${{ number_format($item['item_total_price'], 2) }}</del>
                              <span>${{ number_format($item['item_alternative_quantity'] * $item['item_unit_price'], 2) }}</span>
                            @else
                              <span>${{ number_format($item['item_total_price'], 2) }}</span>
                            @endif
                          </td>
                        </tr>
                      @endif
                    @endforeach
                  </tbody>
                  <tfoot class="bg-neutral-100">
                    <tr>
                      <td colspan="6" class="px-3 py-2 text-normal font-medium text-neutral-800 text-right">Total:</td>
                      <td class="px-3 py-2 text-normal font-bold text-neutral-800 text-right">
                        @if (number_format($total_price, 2) === number_format($alternative_total_price, 2))
                          {{-- no hay cambios --}}
                          <span>$ {{ number_format($total_price, 2) }}</span>
                        @else
                          <del class="text-neutral-400">$ {{ number_format($total_price, 2) }}</del>
                          <span>$ {{ number_format($alternative_total_price, 2) }}</span>
                        @endif
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </x-div-toggle>

          </div>

          {{-- detalle de envio, fecha, y medio de pago informado por el proveedor --}}
          <div class="flex flex-col gap-2 flex-wrap p-4 bg-neutral-100">

            <h4 class="text-sm font-medium text-neutral-700 uppercase tracking-wider">anexo</h4>

            @if ($preorder->is_completed && $preorder->is_approved_by_supplier)
              <div>
                {{-- mensaje de aprobacion del proveedor --}}
                <div class="flex flex-col mt-2">
                  @if ($preorder->is_approved_by_supplier)
                    <span>El proveedor <x-text-tag color="emerald">aceptó</x-text-tag> cumplir con la pre orden según el stock y el presente anexo declarado</span>
                  @endif
                </div>

                {{-- retiro o envio, fecha y método de pago en una línea --}}
                <div class="flex flex-wrap gap-2 w-full mt-2">
                  @if (!empty($preorder_details))
                    {{-- retiro o envio --}}
                    <div class="flex items-center">
                      <span class="font-medium text-sm text-neutral-700">Tipo de entrega:</span>
                      <span class="ml-2 text-sm">
                        {{ implode(', ', $preorder_details['delivery_type']) }}
                      </span>
                    </div>
                    {{-- fecha de envio o retiro --}}
                    <div class="flex items-center">
                      <span class="font-medium text-sm text-neutral-700">Fecha tentativa de entrega o retiro a partir de:</span>
                      <span class="ml-2 text-sm">
                        {{ formatDateTime($preorder_details['delivery_date'], 'd-m-Y') }}
                      </span>
                    </div>
                    {{-- metodo de pago --}}
                    <div class="flex items-center">
                      <span class="font-medium text-sm text-neutral-700">Métodos de pago aceptados:</span>
                      <span class="ml-2 text-sm">
                        {{ implode(', ', $preorder_details['payment_method']) }}
                      </span>
                    </div>
                  @endif
                </div>

                {{-- comentarios --}}
                @if(!empty($preorder_details))
                  <div class="w-full mt-2">
                    <span class="font-medium text-sm text-neutral-700">Comentarios:</span>
                    <p class="mt-1 text-sm text-neutral-700">
                      {{ $preorder_details['short_description'] ?? 'ninguno' }}
                    </p>
                  </div>
                @endif
              </div>
            @else
              <div>
                <span>sin respuesta</span>
              </div>
            @endif

          </div>

        </div>

      </x-slot:content>

      <x-slot:footer class="my-2">

        {{-- botones de guardado: desktop, display desde 1024px --}}
        <div class="hidden lg:flex w-full justify-end gap-2 mt-2">

          @if ($preorder->is_completed && $preorder->is_approved_by_supplier)

            @if (!$preorder->is_approved_by_buyer)

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
                wire:confirm="¿aprobar esta pre orden?, al aprobar la pre orden indica que está de acuerdo con el stock que puede cumplir el proveedor y con los parámetros del anexo. Se emitirá una orden de compra final para todos los suministros y packs de la lista con stock, la orden de compra definitiva se enviará por email al proveedor"
                >aprobar y ordenar compra
              </x-btn-button>

            @else

              <p>Esta pre orden fue aceptada, y se envió al proveedor una orden de compra definitiva.</p>

            @endif

          @endif
        </div>

      </x-slot:footer>

    </x-content-section>

</article>
</div>
