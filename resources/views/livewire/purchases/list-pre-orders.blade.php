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
                fecha de orden
              </x-table-th>
              <x-table-th class="text-start">
                compra registrada?
              </x-table-th>
              <x-table-th class="text-start">
                acciones
              </x-table-th>
            </tr>
          </x-slot:tablehead>
          <x-slot:tablebody>
            @forelse ($preorders as $preorder)
              <tr wire:key="{{ $preorder->id }}">
                <x-table-td class="text-end">
                  {{ $preorder->id }}
                </x-table-td>
                <x-table-td class="text-start">
                  {{ $preorder->pre_order_code }}
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
                  -
                </x-table-td>
                <x-table-td class="text-start">
                  -
                </x-table-td>
                <x-table-td class="text-start">

                  <div class="flex gap-1">
                    <x-a-button
                      wire:navigate
                      href="#"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >ver
                    </x-a-button>

                    <x-a-button
                      wire:navigate
                      href="{{ route('purchases-purchases-create', $preorder->id) }}"
                      bg_color="neutral-100"
                      border_color="neutral-200"
                      text_color="neutral-600"
                      >registrar compra
                    </x-a-button>
                  </div>


                </x-table-td>
              </tr>
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
