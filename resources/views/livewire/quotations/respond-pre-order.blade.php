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
          <span class="text-base lg:text-sm text-neutral-700">¡tiene hasta la fecha: <span class="font-semibold">{{ formatDateTime($preorder->pre_order_period->period_end_at, 'd-m-Y') }}</span> para enviar una respuesta, hasta el cierre de este pedido de pre orden!</span>
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
            @if ($preorder->quotation_reference !== null)
              <div class="mt-2 ">
                <span class="py-2 lowercase">esta pre orden se creó a partir de uno de sus presupuestos recibido el {{ $quotation->updated_at->format('d-m-Y H:i:s') }}</span>
              </div>
            @endif
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

          {{-- items --}}
          <div class="p-4 border-b">

            <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">Detalle:</h4>

            <x-div-toggle x-data="{ open:true }" title="suministros y packs de esta pre orden">

              <x-slot:subtitle>
                @error('items.*')
                  <span class="text-red-400 text-sm capitalize mx-2">¡hay errores en esta sección!</span>
                @enderror
              </x-slot:subtitle>

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
                        ">Tiene Stock?
                        <x-quest-icon title="¿tiene stock para cumplir con la cantidad requerida?" />
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-2 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider
                        ">Cantidad requerida
                        <x-quest-icon title="cantidad que la panadería necesita comprar" />
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
                        {{-- suministro --}}
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
                          {{-- * PROVISIONS --}}
                          {{-- input stock y cantidad alternativa --}}
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                            <div class="flex justify-end items-center gap-2 w-full">
                              @error("items.{$key}.item_has_stock")
                                <span class="text-xs text-red-400">{{ $message }}</span>
                              @enderror

                              <div class="flex items-center gap-2">

                                {{-- texto si / no --}}
                                @if ($items[$key]['item_has_stock'])
                                  <span>si</span>
                                @else
                                  <span>no</span>
                                @endif

                                {{-- checkbox 'item_has_stock' --}}
                                <input
                                  type="checkbox"
                                  id="items_{{ $key }}_item_has_stock"
                                  wire:model.live="items.{{ $key }}.item_has_stock"
                                  @checked(true)
                                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                />

                                {{-- si no tiene stock, indicar cantidad alternativa --}}
                                @if (!$items[$key]['item_has_stock'])
                                  <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1">
                                      <label for="items_{{ $key }}_alternative_quantity" class="text-xs">¿cuanto puede cubrir?:</label>
                                      <input
                                        type="number"
                                        id="items_{{ $key }}_alternative_quantity"
                                        wire:model.live="items.{{ $key }}.item_alternative_quantity"
                                        min="0"
                                        max="{{ $items[$key]['item_quantity'] }}"
                                        class="w-12 p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                      />
                                    </div>
                                    @error("items.{$key}.item_alternative_quantity")
                                      <span class="text-xs text-red-400">{{ $message }}</span>
                                    @enderror
                                  </div>
                                @endif
                              </div>
                            </div>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            {{ $item['item_quantity'] }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            ${{ number_format($item['item_unit_price'], 2) }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                            {{-- precio total, si no tiene stock, calcular por cantidad alternativa --}}
                            @if (!$items[$key]['item_has_stock'])
                              ${{ number_format($items[$key]['item_alternative_quantity'] * $items[$key]['item_unit_price'], 2) }}
                            @else
                              ${{ number_format($item['item_total_price'], 2) }}
                            @endif
                          </td>
                        </tr>
                      @else
                        {{-- pack --}}
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
                          {{-- input stock --}}
                          {{-- * PACKS --}}
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500">
                            <div class="flex justify-end items-center gap-2 w-full">
                              @error("items.{$key}.item_has_stock")
                                <span class="text-xs text-red-400">{{ $message }}</span>
                              @enderror

                              <div class="flex items-center gap-2">

                                {{-- texto si / no --}}
                                @if ($items[$key]['item_has_stock'])
                                  <span>si</span>
                                @else
                                  <span>no</span>
                                @endif

                                {{-- checkbox 'item_has_stock' --}}
                                <input
                                  type="checkbox"
                                  id="items_{{ $key }}_item_has_stock"
                                  wire:model.live="items.{{ $key }}.item_has_stock"
                                  @checked(true)
                                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                />

                                {{-- si no tiene stock, indicar cantidad alternativa --}}
                                @if (!$items[$key]['item_has_stock'])
                                  <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1">
                                      <label for="items_{{ $key }}_alternative_quantity" class="text-xs">¿cuanto puede cubrir?:</label>
                                      <input
                                        type="number"
                                        id="items_{{ $key }}_alternative_quantity"
                                        wire:model.live="items.{{ $key }}.item_alternative_quantity"
                                        min="0"
                                        max="{{ $items[$key]['item_quantity'] }}"
                                        class="w-12 p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                      />
                                    </div>
                                    @error("items.{$key}.item_alternative_quantity")
                                      <span class="text-xs text-red-400">{{ $message }}</span>
                                    @enderror
                                  </div>
                                @endif
                              </div>
                            </div>
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            {{ $item['item_quantity'] }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 text-right">
                            ${{ number_format($item['item_unit_price'], 2) }}
                          </td>
                          <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-800 text-right">
                            {{-- precio total, si no tiene stock, calcular por cantidad alternativa --}}
                            @if (!$items[$key]['item_has_stock'])
                              <del>${{ number_format($item['item_total_price'], 2) }}</del>
                              ${{ number_format($items[$key]['item_alternative_quantity'] * $items[$key]['item_unit_price'], 2) }}
                            @else
                              ${{ number_format($item['item_total_price'], 2) }}
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
                        $ {{ number_format($total_price, 2) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </x-div-toggle>

          </div>

          {{-- formulario con otros datos --}}
          <div class="flex flex-col gap-2 flex-wrap p-4 bg-neutral-100">

            <h4 class="text-sm font-medium text-neutral-600 uppercase tracking-wider mb-2">anexo:</h4>

            <div class="flex justify-start items-start gap-4 w-full flex-wrap">

              {{-- retiro o envio --}}
              <div class="flex flex-col gap-2 justify-start items-start p-1 min-w-fit shrink">
                <span>
                  <span>¿Qué tipo de entrega proporciona?</span>
                  <span class="text-red-500">*</span>
                </span>
                @error('delivery_type')<span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                <div class="flex flex-col gap-1 w-full">
                  <div class="flex items-start gap-1 w-full">
                    <input
                      type="checkbox"
                      name="delivery_type"
                      wire:model.live="delivery_type"
                      value="envio a domicilio"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                    <label>Envíos a domicilio</label>
                  </div>
                  <div class="flex items-start gap-1 w-full">
                    <input
                      type="checkbox"
                      name="delivery_type"
                      wire:model.live="delivery_type"
                      value="retirar en local"
                      class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                    />
                    <label>Retiros en el local</label>
                  </div>
                </div>
              </div>

              {{-- fecha de envio o retiro --}}
              <div class="flex flex-col gap-2 p-1 w-1/4 shrink">
                <span>
                    <label>Fecha posible de envío/retiro a partir de:</label>
                  <span class="text-red-500">*</span>
                  <x-quest-icon title="Indique cual podría ser la fecha en que estarian listos los suministros y/o packs para el envio a domicilio o retiro en el local."/>
                </span>
                @error('delivery_date') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                <input
                  type="date"
                  wire:model="delivery_date"
                  min="{{ now()->format('Y-m-d') }}"
                  class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                />
              </div>

              {{-- metodo de pago --}}
              <div class="flex flex-col gap-2 p-1 min-w-fit shrink">
                <span>
                  <label>Métodos de pago aceptados</label>
                  <span class="text-red-500">*</span>
                  <x-quest-icon title="Seleccione los métodos de pago que acepta para esta pre orden" />
                </span>
                @error('payment_method') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                <div class="grid grid-cols-3 gap-2">
                  <div class="flex items-start gap-1">
                    <input
                      type="checkbox"
                      name="payment_method"
                      wire:model.live="payment_method"
                      value="efectivo"
                      class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                    />
                    <label>Efectivo</label>
                  </div>
                  <div class="flex items-start gap-1">
                    <input
                      type="checkbox"
                      name="payment_method"
                      wire:model.live="payment_method"
                      value="tarjeta de credito"
                      class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                    />
                    <label>Tarjeta de crédito</label>
                  </div>
                  <div class="flex items-start gap-1">
                    <input
                      type="checkbox"
                      name="payment_method"
                      wire:model.live="payment_method"
                      value="tarjeta de debito"
                      class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                    />
                    <label>Tarjeta de débito</label>
                  </div>
                  <div class="flex items-start gap-1">
                    <input
                      type="checkbox"
                      name="payment_method"
                      wire:model.live="payment_method"
                      value="mercado pago"
                      class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                    />
                    <label>Mercado Pago</label>
                  </div>
                  <div class="flex items-start gap-1">
                    <input
                      type="checkbox"
                      name="payment_method"
                      wire:model.live="payment_method"
                      value="uala"
                      class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                    />
                    <label>Ualá</label>
                  </div>
                  <div class="flex items-start gap-1">
                    <input
                      type="checkbox"
                      name="payment_method"
                      wire:model.live="payment_method"
                      value="viumi"
                      class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                    />
                    <label>Viumi</label>
                  </div>
                </div>
              </div>

              {{-- comentarios --}}
              <div class="flex flex-col gap-2 p-1 w-1/2 shrink">
                <span>
                  <label>Comentarios extra</label>
                  <x-quest-icon title="espacio para indicar algun comentario o consideración sobre la pre orden" />
                </span>
                @error('short_description') <span>{{ $message }}</span> @enderror
                <textarea name="short_description" wire:model="short_description" cols="20" rows="2" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
              </div>

              {{-- acepto cumplir con esta pre-orden --}}
              <div class="flex flex-col gap-2 p-1 w-1/3 shrink">
                <span>
                  <span>Terminos del acuerdo para la pre orden</span>
                  <span class="text-red-500">*</span>
                </span>
                @error('accept_terms') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                <div class="flex items-start gap-2">
                  <input
                    type="checkbox"
                    wire:model="accept_terms"
                    class="mt-1 border border-neutral-200 focus:ring focus:ring-neutral-300"
                  />
                  <label class="text-sm text-neutral-700">
                    Confirmo que he leído toda la pre orden y puedo cumplir con todos los suministros y/o packs solicitados según mi stock disponible, así como con los terminos del presente anexo.
                  </label>
                </div>
              </div>

            </div>

          </div>

        </div>

      </x-slot:content>

      <x-slot:footer class="my-2">

        {{-- botones de guardado --}}
        <div class="flex justify-end items-center gap-2 w-full mt-2">

          <x-a-button
            wire:navigate
            href="{{ route('quotations-preorders-index') }}"
            bg_color="neutral-600"
            border_color="neutral-600"
            >cancelar
          </x-a-button>

          <x-btn-button
            type="button"
            wire:click="save"
            wire:confirm="¿confirma de ha leido toda la pre orden y puede cumplir con todos los suministros y/o packs solicitados según su stock?"
            >responder
          </x-btn-button>

        </div>

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
