<div>
  {{-- componente responder pre orden --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section 
      title="{{ 
        $is_editing
          ? 'editar respuesta pre orden'
          : 'responder pre orden de compras'
      }}">
      <x-slot:title>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">
          {{ 
            $is_editing
              ? 'editar respuesta pre orden'
              : 'responder pre orden de compras'
          }}
        </span>
      </x-slot:title>
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- estado, evaluacion y presupuesto de referencia --}}
        <div class="flex items-center justify-start gap-2">
          {{-- estado de pre orden --}}
          @if ($preorder->is_completed)
            <x-text-tag
              color="emerald"
              class="cursor-pointer"
              >respondido
              <x-quest-icon title="ya ha respondido"/>
            </x-text-tag>
          @else
            <x-text-tag
              color="neutral"
              class="cursor-pointer"
              >sin responder
              <x-quest-icon title="no ha respondido"/>
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
              <x-quest-icon title="tanto usted como proveedor y la panaderia estan de acuerdo con esta pre orden de compra"/>
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
              >referencia: <span class="text-xs font-semibold uppercase">{{ $preorder->quotation_reference }}</span>
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
      </x-slot:header>

      <x-slot:content class="flex-col overflow-y-auto">
        <section>
          {{-- encabezado --}}
          <header class="flex w-full gap-8 p-2 border rounded-md border-neutral-200">
            {{-- proveedor --}}
            <div class="flex flex-col gap-1">
              <span class="font-semibold uppercase text-md text-neutral-500">de:</span>
              <span>
                <span class="font-semibold">Proveedor:</span>
                <span>{{ $quotation->supplier->company_name }}</span>
              </span>
              <span>
                <span class="font-semibold">CUIT:</span>
                <span>{{ $quotation->supplier->company_cuit }}</span>
              </span>
              <span>
                <span class="font-semibold">Teléfono:</span>
                <span>{{ $quotation->supplier->phone_number }}</span>
              </span>
              <span>
                <span class="font-semibold">Correo:</span>
                <span>{{ $quotation->supplier->user->email }}</span>
              </span>
              <span>
                <span class="font-semibold">Dirección:</span>
                <span>{{ $quotation->supplier->full_address }}</span>
              </span>
            </div>
            {{-- panaderia --}}
            <div class="flex flex-col gap-1">
              <span class="font-semibold uppercase text-md text-neutral-500">para:</span>
              <span>
                <span class="font-semibold">Panadería:</span>
                <span>{{ $razon_social }}</span>
              </span>
              <span>
                <span class="font-semibold">CUIT:</span>
                <span>{{ $cuit }}</span>
              </span>
              <span>
                <span class="font-semibold">Inicio de actividades:</span>
                <span>{{ $inicio_actividades }}</span>
              </span>
              <span>
                <span class="font-semibold">Teléfono:</span>
                <span>{{ $telefono }}</span>
              </span>
              <span>
                <span class="font-semibold">Correo:</span>
                <span>{{ $correo }}</span>
              </span>
              <span>
                <span class="font-semibold">Dirección:</span>
                <span>{{ $direccion }}</span>
              </span>
            </div>
          </header>
          {{-- contenido --}}
          <x-div-toggle x-data="{ open:true }" class="w-full p-2">
            
            <x-slot:title>
              suministros y packs de esta pre orden
            </x-slot:title>

            <x-slot:subtitle>
              complete la disponibilidad para cada producto:
            </x-slot:subtitle>
            
            <x-slot:message>
              @error('items.*')
                <span class="mx-2 text-sm text-red-400 capitalize">¡hay errores en esta sección!</span>
              @enderror
            </x-slot:message>
            
            <div class="overflow-x-auto overflow-y-auto max-h-72">
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-100">
                    <x-table-th class="text-end">
                      #
                    </x-table-th>
                    <x-table-th class="text-start">
                      suministro/pack
                    </x-table-th>
                    <x-table-th class="text-start">
                      marca/tipo/volumen
                      <x-quest-icon title="kilogramos (K), gramos (g), litros (L), mililitros (mL), metros (M), centimetros (cm), unidades (U)" />
                    </x-table-th>
                    <x-table-th class="text-end">
                      tiene stock?
                      <x-quest-icon title="¿actualmente tiene en stock la cantidad requerida? puede proporcionar todo o una cantidad alternativa, si no tiene, indique 0" />
                    </x-table-th>
                    <x-table-th class="text-end">
                      cantidad requerida
                      <x-quest-icon title="cantidad que la panaderia desea comprar" />
                    </x-table-th>
                    <x-table-th class="text-end">
                      $precio unitario
                    </x-table-th>
                    <x-table-th class="text-end">
                      $subtotal
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @foreach($items as $key => $item)
                    @if ($item['item_type'] === $item_provision)
                      {{-- suministro --}}
                      <tr class="border">
                        <x-table-td class="text-end">
                          {{ $key+1 }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $item['item_object']->provision_name }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          <span>
                            <span>{{ $item['item_object']->trademark->provision_trademark_name }}/{{ $item['item_object']->type->provision_type_name }}</span>
                            <span class="lowercase">/{{ convert_measure($item['item_object']->provision_quantity, $item['item_object']->measure) }}</span>
                          </span>
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{-- input stock y cantidad alternativa --}}
                          <div class="flex items-center justify-end w-full gap-2">
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
                                    <label for="items_{{ $key }}_alternative_quantity" class="text-xs">
                                      ¿cuanto puede cubrir?:
                                    </label>
                                    <input
                                      type="number"
                                      id="items_{{ $key }}_alternative_quantity"
                                      wire:model.live="items.{{ $key }}.item_alternative_quantity"
                                      min="0"
                                      max="{{ $items[$key]['item_quantity'] }}"
                                      value="{{ $items[$key]['item_alternative_quantity'] }}" {{-- valor desde BD --}}
                                      class="w-12 p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                    />
                                  </div>
                                  @error("items.{$key}.item_alternative_quantity")
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                  @enderror

                                  @if($is_editing && $items[$key]['item_alternative_quantity'] > 0)
                                    <span class="text-xs text-blue-600">
                                      Cantidad alternativa anterior: {{ $original_alternative_quantities[$key]['quantity'] }}
                                    </span>
                                  @endif
                                </div>
                              @endif
                            </div>
                          </div>
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{ $item['item_quantity'] }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          ${{ toMoneyFormat($item['item_unit_price']) }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{-- precio total. si no tiene stock, calcular por cantidad alternativa --}}
                          @if (!$items[$key]['item_has_stock'])
                            <del>${{ toMoneyFormat($item['item_total_price']) }}</del>
                            ${{ toMoneyFormat($items[$key]['item_alternative_quantity'] * $items[$key]['item_unit_price']) }}
                          @else
                            ${{ toMoneyFormat($item['item_total_price']) }}
                          @endif
                        </x-table-td>
                      </tr>
                    @else
                      {{-- pack --}}
                      <tr class="border">
                        <x-table-td class="text-end">
                          {{ $key+1 }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $item['item_object']->pack_name }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          <span>
                            <span>{{ $item['item_object']->provision->trademark->provision_trademark_name }}/ {{ $item['item_object']->provision->type->provision_type_name }}</span>
                            <span class="lowercase">/{{ convert_measure($item['item_object']->pack_quantity, $item['item_object']->provision->measure) }}</span>
                          </span>
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{-- input stock y cantidad alternativa --}}
                          <div class="flex items-center justify-end w-full gap-2">
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
                                    <label for="items_{{ $key }}_alternative_quantity" class="text-xs">
                                      ¿cuanto puede cubrir?:
                                    </label>
                                    <input
                                      type="number"
                                      id="items_{{ $key }}_alternative_quantity"
                                      wire:model.live="items.{{ $key }}.item_alternative_quantity"
                                      min="0"
                                      max="{{ $items[$key]['item_quantity'] }}"
                                      value="{{ $items[$key]['item_alternative_quantity'] }}" {{-- valor desde BD --}}
                                      class="w-12 p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                                    />
                                  </div>
                                  @error("items.{$key}.item_alternative_quantity")
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                  @enderror
                                  @if($is_editing && $items[$key]['item_alternative_quantity'] > 0)
                                    <span class="text-xs text-blue-600">
                                      Cantidad alternativa anterior: {{ $original_alternative_quantities[$key]['quantity'] }}
                                    </span>
                                  @endif
                                </div>
                              @endif
                            </div>
                          </div>
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{ $item['item_quantity'] }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          ${{ toMoneyFormat($item['item_unit_price']) }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          {{-- precio total, si no tiene stock, calcular por cantidad alternativa --}}
                          @if (!$items[$key]['item_has_stock'])
                            <del>${{ toMoneyFormat($item['item_total_price']) }}</del>
                            ${{ toMoneyFormat($items[$key]['item_alternative_quantity'] * $items[$key]['item_unit_price']) }}
                          @else
                            ${{ toMoneyFormat($item['item_total_price']) }}
                          @endif
                        </x-table-td>
                      </tr>
                    @endif
                  @endforeach
                  <tfoot>
                    <tr class="border">
                      <x-table-td colspan="6" class="font-semibold capitalize text-end">Total:</x-table-td>
                      <x-table-td class="font-semibold text-end">
                        ${{ toMoneyFormat($total_price) }}
                      </x-table-td>
                    </tr>
                  </tfoot>
                </x-slot:tablebody>
              </x-table-base>
            </div>
          </x-div-toggle>
          {{-- anexo --}}
          <x-div-toggle x-data="{ open:true}" class="w-full p-2">
            {{-- formulario con otros datos --}}
            <div class="flex flex-col flex-wrap gap-2 p-4 bg-neutral-100">

              <h4 class="mb-2 text-sm font-medium tracking-wider uppercase text-neutral-600">anexo:</h4>

              <div class="flex flex-wrap items-start justify-start w-full gap-4">

                {{-- retiro o envio --}}
                <div class="flex flex-col items-start justify-start gap-2 p-1 min-w-fit shrink">
                  <span>
                    <span>¿Qué tipo de entrega proporciona?</span>
                    <span class="text-red-500">*</span>
                  </span>
                  @error('delivery_type')<span class="text-sm text-red-400">{{ $message }}</span> @enderror
                  <div class="flex flex-col w-full gap-1">
                    <div class="flex items-start w-full gap-1">
                      <input
                        type="checkbox"
                        name="delivery_type"
                        wire:model.live="delivery_type"
                        value="envio a domicilio"
                        class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
                      />
                      <label>Envíos a domicilio</label>
                    </div>
                    <div class="flex items-start w-full gap-1">
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
                <div class="flex flex-col w-1/4 gap-2 p-1 shrink">
                  <span>
                      <label>Fecha posible de envío/retiro a partir de:</label>
                    <span class="text-red-500">*</span>
                    <x-quest-icon title="Indique cual podría ser la fecha en que estarian listos los suministros y/o packs para el envio a domicilio o retiro en el local."/>
                  </span>
                  @error('delivery_date') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
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
                  @error('payment_method') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
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
                <div class="flex flex-col w-1/2 gap-2 p-1 shrink">
                  <span>
                    <label>Comentarios extra</label>
                    <x-quest-icon title="espacio para indicar algun comentario o consideración sobre la pre orden" />
                  </span>
                  @error('short_description') <span>{{ $message }}</span> @enderror
                  <textarea name="short_description" wire:model="short_description" cols="20" rows="2" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
                </div>

                {{-- acepto cumplir con esta pre-orden --}}
                <div class="flex flex-col w-1/3 gap-2 p-1 shrink">
                  <span>
                    <span>Terminos del acuerdo para la pre orden</span>
                    <span class="text-red-500">*</span>
                  </span>
                  @error('accept_terms') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
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
          </x-div-toggle>
        </section>

      </x-slot:content>

      <x-slot:footer class="my-2">

        {{-- botones de guardado --}}
        <div class="flex items-center justify-end w-full gap-2 mt-2">

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
            wire:confirm="{{ 
              $is_editing 
                ? '¿Confirma los cambios realizados?'
                : '¿confirma que ha leido toda la pre orden y puede cumplir con todos los suministros y/o packs solicitados según su stock?'
              }}"
            >{{ 
              $is_editing 
                ? 'actualizar respuesta' 
                : 'responder' 
            }}
          </x-btn-button>

        </div>

      </x-slot:footer>

    </x-content-section>

  </article>
</div>
