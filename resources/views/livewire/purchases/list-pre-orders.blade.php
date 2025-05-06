<div>
  {{-- *MODULO DE COMPRAS --}}
  {{-- componente listar pre ordenes --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="lista de pre ordenes">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="">

        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">

          {{-- termino de busqueda --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="">buscar preorden</label>
            <input
              type="text"
              name="search_preorder"
              wire:model.live="search_preorder"
              wire:click="resetPagination()"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>

          {{-- filtrar por estado --}}
          <div class="flex flex-col justify-end w-56">
            <label for="">estado</label>
            <select
              name="status_filter"
              id="status_filter"
              wire:model.live="status_filter"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione ...</option>
              <option value="aprobado">aprobado</option>
              <option value="pendiente">pendiente</option>
              <option value="rechazado">rechazado</option>
            </select>
          </div>

          {{-- filtrar por estado de la compra --}}
          <div class="flex flex-col justify-end w-56">
            <label for="">estado de la compra</label>
            <select
              name="status_purchase_filter"
              id="status_purchase_filter"
              wire:model.live="status_purchase_filter"
              wire:click="resetPagination()"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
              >
              <option value="">seleccione ...</option>
              <option value="registrada">registrada</option>
              <option value="pendiente">pendiente</option>
            </select>
          </div>

          {{-- fecha de inicio --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_start_at">fecha de preorden desde</label>
            <input
              type="date"
              name="search_start_at"
              id="search_start_at"
              wire:model.live="search_start_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

          {{-- fecha de fin --}}
          <div class="flex flex-col justify-end w-1/6">
            <label for="search_end_at">fecha de preorden hasta</label>
            <input
              type="date"
              name="search_end_at"
              id="search_end_at"
              wire:model.live="search_end_at"
              class="w-full text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"/>
          </div>

        </div>

        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            wire:click="resetSearchInputs()"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>

      </x-slot:header>

      <x-slot:content>

        <x-table-base>
          <x-slot:tablehead>
            <tr class="border bg-neutral-100">
              <x-table-th class="text-end w-12">
                id
              </x-table-th>
              <x-table-th class="text-start">
                preorden
              </x-table-th>
              <x-table-th class="text-start">
                proveedor
              </x-table-th>
              <x-table-th class="text-start">
                estado
              </x-table-th>
              <x-table-th class="text-start">
                compra registrada?
              </x-table-th>
              <x-table-th class="text-start">
                fecha de preorden
              </x-table-th>
              <x-table-th class="text-start">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($preorders as $preorder)
              @if (
                $status_purchase_filter === '' ||
                ($status_purchase_filter === 'registrada' && $this->checkAsociatedPurchase($preorder)) ||
                ($status_purchase_filter === 'pendiente' && !$this->checkAsociatedPurchase($preorder))
              )
                <tr wire:key="{{ $preorder->id }}">
                  <x-table-td class="text-end">
                    {{ $preorder->id }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    <span class="text-xs uppercase">{{ $preorder->pre_order_code }}</span>
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $preorder->supplier->company_name }}
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{-- evaluacion --}}
                    @if ($preorder->status === $status_pending)
                      <x-text-tag
                        color="neutral"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="pendiente de evaluación"/>
                      </x-text-tag>
                    @elseif ($preorder->status === $status_approved)
                      <x-text-tag
                        color="emerald"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="aprobó esta pre orden para crear una orden definitiva"/>
                      </x-text-tag>
                    @else
                      <x-text-tag
                        color="red"
                        class="cursor-pointer"
                        >{{ $preorder->status }}
                        <x-quest-icon title="esta pre orden fue rechazada por alguna de las partes"/>
                      </x-text-tag>
                    @endif
                  </x-table-td>
                  <x-table-td class="text-start">
                    @if ($preorder->status === $status_approved)
                      @if ($this->checkAsociatedPurchase($preorder))
                        <x-text-tag
                          color="emerald"
                          class="cursor-pointer"
                          >registrada
                          <x-quest-icon title="Esta preorden ya tiene una compra registrada"/>
                        </x-text-tag>
                      @else
                        <x-text-tag
                          color="neutral"
                          class="cursor-pointer"
                          >pendiente
                          <x-quest-icon title="Esta preorden aún no tiene una compra registrada"/>
                        </x-text-tag>
                      @endif
                    @else
                      -
                    @endif
                  </x-table-td>
                  <x-table-td class="text-start">
                    {{ $preorder->created_at->format('d-m-Y') }}
                  </x-table-td>
                  <x-table-td class="text-start">

                    <div class="flex gap-1">
                      <x-a-button
                        wire:navigate
                        href="#"
                        bg_color="neutral-100"
                        border_color="neutral-200"
                        text_color="neutral-600"
                        >ver preorden
                      </x-a-button>

                      @if ($preorder->status === $status_approved)
                        @if (!$this->checkAsociatedPurchase($preorder))
                          <x-a-button
                            wire:navigate
                            href="{{ route('purchases-purchases-create', $preorder->id) }}"
                            bg_color="neutral-100"
                            border_color="neutral-200"
                            text_color="neutral-600"
                            >registrar compra
                          </x-a-button>
                        @else
                          <x-a-button
                            wire:navigate
                            href="#"
                            wire:click="goToPurchase({{ $preorder }})"
                            bg_color="neutral-100"
                            border_color="neutral-200"
                            text_color="neutral-600"
                            >ver compra
                          </x-a-button>
                        @endif
                      @endif

                    </div>


                  </x-table-td>
                </tr>
              @endif
            @empty
              <tr class="border">
                <td colspan="5">¡sin registros!</td>
              </tr>
            @endforelse
          </x-slot:tablebody>
        </x-table-base>

      </x-slot:content>

      <x-slot:footer class="py-2">
        {{ $preorders->links() }}
      </x-slot:footer>

    </x-content-section>

  </article>
</div>
