<div>
  {{-- componente ver respuesta de pre orden --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="responder pre orden de compras">

      <x-slot:title>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">ver pre orden,&nbsp;</span>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">código:&nbsp;</span>
        <span class="text-sm font-semibold uppercase">{{ $preorder->pre_order_code }}.</span>
        <span class="text-xl md:text-base lg:text-sm text-neutral-800">fecha de la pre orden:&nbsp;</span>
        <span class="font-semibold">{{ formatDateTime($preorder->updated_at, 'd-m-Y H:i:s') }} (último cambio).</span>
      </x-slot:title>

      <x-a-button
        wire:navigate
        href="{{ route('quotations-preorders-index') }}"
        bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600"
        >volver
      </x-a-button>

    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- estado, evaluacion y presupuesto de referencia --}}
        <div class="flex items-center justify-start gap-2">

          {{-- estado de pre orden --}}
          @if ($preorder->is_completed)
          <x-text-tag color="emerald" class="cursor-pointer">respondido
            <x-quest-icon title="ha respondido" />
          </x-text-tag>
          @else
          <x-text-tag color="neutral" class="cursor-pointer">sin responder
            <x-quest-icon title="no ha respondido" />
          </x-text-tag>
          @endif

          {{-- evaluacion pendiente --}}
          @if ($preorder->status === $status_pending)
          <x-text-tag color="neutral" class="cursor-pointer">{{ $preorder->status }}
            <x-quest-icon title="esta pre orden esta aún en tramite de aprobación" />
          </x-text-tag>
          @endif

          {{-- evaluacion aprobado --}}
          @if ($preorder->status === $status_approved)
          <x-text-tag color="emerald" class="cursor-pointer">{{ $preorder->status }}
            <x-quest-icon title="la panaderia esta de acuerdo con esta pre orden de compra" />
          </x-text-tag>
          @endif

          {{-- evaluacion rechazado --}}
          @if ($preorder->status === $status_rejected)
          <x-text-tag color="red" class="cursor-pointer">{{ $preorder->status }}
            <x-quest-icon title="una de las partes rechazó esta pre orden de compra" />
          </x-text-tag>
          @endif

          {{-- si la pre orden se basa en un presupuesto previo --}}
          @if ($preorder->quotation_reference !== null)

          <x-text-tag color="neutral" class="cursor-pointer">referencia: <span
              class="text-xs font-semibold uppercase">{{ $preorder->quotation_reference }}</span>
          </x-text-tag>

          <x-a-button wire:navigate href="{{ route('suppliers-budgets-response', $quotation->id) }}"
            bg_color="neutral-100" border_color="neutral-200" text_color="neutral-600">ver presupuesto previo
          </x-a-button>

          @endif
        </div>
      </x-slot:header>

      <x-slot:content class="flex-col overflow-x-auto overflow-y-auto">
        {{-- una pre orden --}}
        <section>
          {{-- encabezado --}}
          <header class="flex w-full gap-8 p-2 border rounded-md border-neutral-200">
            {{-- panaderia --}}
            <div class="flex flex-col gap-1">
              <span class="font-semibold uppercase text-neutral-500">de:</span>
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
            {{-- proveedor --}}
            <div class="flex flex-col gap-1">
              <span class="font-semibold uppercase text-neutral-500">para:</span>
              <span>
                <span class="font-semibold">Proveedor:</span>
                <span>{{ $preorder->supplier->company_name }}</span>
              </span>
              <span>
                <span class="font-semibold">CUIT:</span>
                <span>{{ $preorder->supplier->company_cuit }}</span>
              </span>
              <span>
                <span class="font-semibold">Teléfono:</span>
                <span>{{ $preorder->supplier->phone_number }}</span>
              </span>
              <span>
                <span class="font-semibold">Correo:</span>
                <span>{{ $preorder->supplier->user->email }}</span>
              </span>
              <span>
                <span class="font-semibold">Dirección:</span>
                <span>{{ $preorder->supplier->full_address }}</span>
              </span>
            </div>
          </header>
          {{-- contenido --}}
          <x-div-toggle x-data="{ open: true }" class="p-2 mt-2" title="suministros y packs de esta pre orden">
            <div class="w-full overflow-x-auto overflow-y-auto max-h-72">
              <x-table-base>
                <x-slot:tablehead>
                  <tr class="border bg-neutral-100">
                    <x-table-th class="text-end">
                      #
                    </x-table-th>
                    <x-table-th class="text-start">
                      Suministro/Pack
                    </x-table-th>
                    <x-table-th class="text-start">
                      Marca/Tipo/volumen
                    </x-table-th>
                    <x-table-th class="text-start">
                      Tiene Stock de la cantidad?
                    </x-table-th>
                    <x-table-th class="text-end">
                      Cantidad
                    </x-table-th>
                    <x-table-th class="text-end">
                      $Precio unitario
                    </x-table-th>
                    <x-table-th class="text-end">
                      $Subtotal
                    </x-table-th>
                  </tr>
                </x-slot:tablehead>
                <x-slot:tablebody>
                  @foreach($items as $key => $item)
                  @if ($item['item_type'] === $item_provision)
                  {{-- suministros --}}
                  <tr class="border">
                    <x-table-td class="text-end">
                      {{ $key+1 }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $item['item_object']->provision_name }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      <span>
                        <span>{{ $item['item_object']->trademark->provision_trademark_name }}/{{
                          $item['item_object']->type->provision_type_name }}</span>
                        <span class="lowercase"> de {{ convert_measure($item['item_object']->provision_quantity,
                          $item['item_object']->measure) }}</span>
                      </span>
                    </x-table-td>
                    <x-table-td class="text-start">
                      @if ($preorder->is_completed)
                      {{ ($item['item_has_stock']) ? 'si' : 'no' }}
                      @else
                      <x-text-tag color="neutral" class="cursor-pointer">sin respuesta
                        <x-quest-icon title="el proveedor no ha respondido" />
                      </x-text-tag>
                      @endif
                    </x-table-td>
                    <x-table-td class="text-end">
                      @if(!$item['item_has_stock'])
                      <del class="text-neutral-400">{{ $item['item_quantity'] }}</del>
                      <span>cantidad alternativa:&nbsp;{{ $item['item_alternative_quantity'] }}</span>
                      @else
                      <span>{{ $item['item_quantity'] }}</span>
                      @endif
                    </x-table-td>
                    <x-table-td class="text-end">
                      <span>${{ toMoneyFormat($item['item_unit_price']) }}</span>
                    </x-table-td>
                    <x-table-td class="text-end">
                      @if(!$item['item_has_stock'])
                      <del class="text-neutral-400">${{ number_format($item['item_total_price'], 2) }}</del>
                      <span>${{ toMoneyFormat($item['item_alternative_quantity'] * $item['item_unit_price']) }}</span>
                      @else
                      <span>${{ toMoneyFormat($item['item_total_price']) }}</span>
                      @endif
                    </x-table-td>
                  </tr>
                  @else
                  {{-- packs --}}
                  <tr class="border">
                    <x-table-td class="text-end">
                      {{ $key+1 }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      {{ $item['item_object']->pack_name }}
                    </x-table-td>
                    <x-table-td class="text-start">
                      <span>
                        <span>{{ $item['item_object']->provision->trademark->provision_trademark_name }}/{{
                          $item['item_object']->provision->type->provision_type_name }}</span>
                        <span class="lowercase"> de {{ convert_measure($item['item_object']->pack_quantity,
                          $item['item_object']->provision->measure) }}</span>
                      </span>
                    </x-table-td>
                    <x-table-td class="text-start">
                      @if ($preorder->is_completed)
                      {{ ($item['item_has_stock']) ? 'si' : 'no' }}
                      @else
                      <x-text-tag color="neutral" class="cursor-pointer">sin respuesta
                        <x-quest-icon title="el proveedor no ha respondido" />
                      </x-text-tag>
                      @endif
                    </x-table-td>
                    <x-table-td class="text-end">
                      @if(!$item['item_has_stock'])
                      <del class="text-neutral-400">{{ $item['item_quantity'] }}</del>
                      <span>cantidad alternativa:&nbsp;{{ $item['item_alternative_quantity'] }}</span>
                      @else
                      <span>{{ $item['item_quantity'] }}</span>
                      @endif
                    </x-table-td>
                    <x-table-td class="text-end">
                      <span>${{ toMoneyFormat($item['item_unit_price']) }}</span>
                    </x-table-td>
                    <x-table-td class="text-end">
                      @if(!$item['item_has_stock'])
                      <del class="text-neutral-400">${{ number_format($item['item_total_price'], 2) }}</del>
                      <span>${{ toMoneyFormat($item['item_alternative_quantity'] * $item['item_unit_price']) }}</span>
                      @else
                      <span>${{ toMoneyFormat($item['item_total_price']) }}</span>
                      @endif
                    </x-table-td>
                  </tr>
                  @endif
                  @if ($loop->last)
                  <tr class="border">
                    <x-table-td colspan="6" class="font-semibold capitalize text-end">Total:</x-table-td>
                    <x-table-td class="text-end">
                      @if (number_format($total_price, 2) === number_format($alternative_total_price, 2))
                      {{-- no hay cambios --}}
                      <span class="font-semibold">${{ toMoneyFormat($total_price) }}</span>
                      @else
                      <del class="text--400">$ {{ toMoneyFormat($total_price) }}</del>
                      <span class="font-semibold">${{ toMoneyFormat($alternative_total_price) }}</span>
                      @endif
                    </x-table-td>
                  </tr>
                  @endif
                  @endforeach
                </x-slot:tablebody>
              </x-table-base>
            </div>
          </x-div-toggle>
          {{-- anexo --}}
          <x-div-toggle x-data="{ open: true }" class="p-2 mt-2" title="anexo">
            {{-- detalle de envio, fecha, y medio de pago informado por el proveedor --}}
            <div class="flex flex-col gap-2">
              @if ($preorder->is_completed && $preorder->is_approved_by_supplier)
              <div class="flex flex-col gap-2">
                {{-- mensaje de aprobacion del proveedor --}}
                <div class="flex flex-col">
                  @if ($preorder->is_approved_by_supplier)
                  <span>Usted <x-text-tag color="emerald">aceptó</x-text-tag> cumplir con la pre orden según el
                    stock y el presente anexo declarado</span>
                  @endif
                </div>
                {{-- retiro o envio, fecha y método de pago en una línea --}}
                <div class="flex flex-wrap w-full gap-2">
                  @if (!empty($preorder_details))
                  {{-- retiro o envio --}}
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-neutral-700">Tipo de entrega:</span>
                    <span class="ml-2 text-sm">
                      {{ implode(', ', $preorder_details['delivery_type']) }}
                    </span>
                  </div>
                  {{-- fecha de envio o retiro --}}
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-neutral-700">Fecha tentativa de entrega o retiro a partir
                      de:</span>
                    <span class="ml-2 text-sm">
                      {{ formatDateTime($preorder_details['delivery_date'], 'd-m-Y') }}
                    </span>
                  </div>
                  {{-- metodo de pago --}}
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-neutral-700">Métodos de pago aceptados:</span>
                    <span class="ml-2 text-sm">
                      {{ implode(', ', $preorder_details['payment_method']) }}
                    </span>
                  </div>
                  @endif
                </div>
                {{-- comentarios --}}
                @if(!empty($preorder_details))
                <div class="w-full">
                  <span class="text-sm font-medium text-neutral-700">Comentarios:</span>
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
          </x-div-toggle>
        </section>

      </x-slot:content>

      <x-slot:footer class="my-2">
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
