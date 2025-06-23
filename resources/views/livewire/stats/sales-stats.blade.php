<div>
  {{-- componente estadisticas de venta --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="estadisticas de ventas">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- busqueda --}}
        <div class="flex items-end justify-start gap-1">
          {{-- fecha inicio --}}
          <div class="flex flex-col justify-end w-56">
            <label for="start_date">ventas desde fecha:</label>
            <input
              type="date"
              name="start_date"
              wire:model.live="start_date"
              placeholder="ingrese un id, o termino de busqueda"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- fecha fin --}}
          <div class="flex flex-col justify-end w-56">
            <label for="end_date">ventas hasta fecha:</label>
            <input
              type="date"
              name="end_date"
              wire:model.live="end_date"
              placeholder="ingrese un id, o termino de busqueda"
              class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- productos --}}
          <div class="flex flex-col justify-end w-fit">
            <label for="product">productos:</label>
            <select wire:model.live="product" name="product" id="product" class="p-1 pr-8 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
              <option value="">todos ...</option>
              @forelse ($all_products as $product)
                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
              @empty
                <option value="">sin productos.</option>
              @endforelse
            </select>
          </div>
          {{-- limpiar campos de busqueda --}}
          <x-a-button
            wire:click="clearFilters"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            class="cursor-pointer"
            >limpiar filtros
          </x-a-button>
        </div>
      </x-slot:header>

      <x-slot:content>
        <div class="flex flex-col w-full gap-4">
          {{-- contenedor del grafico --}}
          <div class="flex items-center justify-start">
            {{-- grafico --}}
            <div class="flex items-center justify-center p-2 border rounded-sm border-neutral-300" style="position: relative; height: 400px; width: 100%;">
              <canvas id="sales_chart"></canvas>
            </div>
          </div>

          @if ($totalItems > 0)
            {{-- tabla de datos --}}
            <x-table-base>
              <x-slot:tablehead>
                  <tr class="border bg-neutral-100">
                    <x-table-th class="text-end">
                      #
                    </x-table-th>
                    <x-table-th class="text-end">
                      fecha de venta
                    </x-table-th>
                    <x-table-th class="text-start">
                      producto
                    </x-table-th>
                    <x-table-th class="text-end">
                      cantidad vendida
                    </x-table-th>
                    <x-table-th class="text-end">
                      $total vendido
                    </x-table-th>
                  </tr>
              </x-slot:tablehead>
              <x-slot:tablebody>
                  @forelse ($sales as $index => $sale)
                    <tr class="border" wire:key="{{ $index }}">
                      <x-table-td class="text-end">
                        {{ $startItem + $index }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ $sale['date'] }}
                      </x-table-td>
                      <x-table-td class="text-start">
                        {{ $sale['product'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        {{ $sale['quantity_sold'] }}
                      </x-table-td>
                      <x-table-td class="text-end">
                        ${{ toMoneyFormat($sale['total']) }}
                      </x-table-td>
                    </tr>
                  @empty
                    <tr class="border">
                      <x-table-td colspan="5" class="py-4 text-center">
                        @if($start_date || $end_date)
                          No se encontraron ventas en el rango de fechas seleccionado
                        @else
                          ¡Sin registros de ventas!
                        @endif
                      </x-table-td>
                    </tr>
                  @endforelse
              </x-slot:tablebody>
            </x-table-base>
          @else
            <div class="py-4 text-center text-neutral-500">
              <span>No se encontraron ventas que coincidan con los filtros aplicados</span>
            </div>
          @endif

        </div>
      </x-slot:content>

      <x-slot:footer class="">
        {{-- paginacion --}}
        <div class="flex flex-col w-full gap-1 p-2">
          {{-- informacion de paginacion --}}
          @if($totalItems > 0)
            <div class="flex items-center justify-between p-2 text-sm rounded text-neutral-600 bg-neutral-50">
              <span>
                Mostrando {{ $startItem }} - {{ $endItem }} de {{ $totalItems }} registros
              </span>
              <span>
                Página {{ $currentPage }} de {{ $totalPages }}
              </span>
            </div>
          @endif
  
          {{-- controles de paginacion --}}
          @if($totalPages > 1 && $totalItems > 0)
            <div class="flex items-center justify-end w-full gap-2">
              {{-- grupo de botones --}}
              <div class="flex items-end gap-2">
                {{-- boton pagina anterior --}}
                @if($hasPrevPage)
                  <button 
                    wire:click="prevPage"
                    wire:key="page-btn-prev"
                    class="px-3 py-1 text-sm bg-white border rounded border-neutral-300 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >« Anterior
                  </button>
                @endif

                {{-- numeros de pagina --}}
                @php
                  $start = max(1, $currentPage - 2);
                  $end = min($totalPages, $currentPage + 2);
                @endphp
  
                @if($start > 1)
                  <button wire:click="goToPage(1)" wire:key="page-btn-1" class="px-3 py-1 text-sm bg-white border rounded border-neutral-300 hover:bg-neutral-50">1</button>
                  @if($start > 2)
                    <span wire:key="page-btn-dots-start" class="px-2 text-neutral-500">...</span>
                  @endif
                @endif
  
                @for($i = $start; $i <= $end; $i++)
                  <button 
                    wire:click="goToPage({{ $i }})"
                    wire:key="page-btn-{{ $i }}"
                    class="px-3 py-1 text-sm border rounded {{ $i == $currentPage ? 'bg-blue-500 text-white border-blue-500' : 'bg-white border-neutral-300 hover:bg-neutral-50' }}"
                  >
                    {{ $i }}
                  </button>
                @endfor
  
                @if($end < $totalPages)
                  @if($end < $totalPages - 1)
                    <span wire:key="page-btn-dots-end" class="px-2 text-neutral-500">...</span>
                  @endif
                  <button wire:key="page-btn-{{ $totalPages }}" wire:click="goToPage({{ $totalPages }})" class="px-3 py-1 text-sm bg-white border rounded border-neutral-300 hover:bg-neutral-50">{{ $totalPages }}</button>
                @endif
  
                {{-- boton pagina siguiente --}}
                @if($hasNextPage)
                  <button 
                    wire:click="nextPage"
                    wire:key="page-btn-next"
                    class="px-3 py-1 text-sm bg-white border rounded border-neutral-300 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    Siguiente »
                  </button>
                @endif
              </div>
            </div>
          @endif
        </div>
      </x-slot:footer>

    </x-content-section>
  </article>

  @script
    <script>

      const ctx = document.getElementById('sales_chart');
      let chart;

      function initChart(chartData) {
        if (chart) {
          chart.destroy();
        }

        if (!chartData.labels || !chartData.datasets || chartData.labels.length === 0) {
          console.log('No hay datos para mostrar en el gráfico');
          return;
        }

        chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: chartData.labels,
            datasets: chartData.datasets.map((dataset, index) => ({
              label: dataset.label,
              data: dataset.data,
              backgroundColor: getColor(index, 0.7),
              borderColor: getColor(index, 1),
              borderWidth: 1,
              stack: 'stack1'
            }))
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
              intersect: false,
              mode: 'index'
            },
            plugins: {
              title: {
                display: true,
                text: 'Ventas por Producto y Fecha',
                font: { size: 16 }
              },
              legend: {
                display: true,
                position: 'top'
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return context.dataset.label + ': $' + 
                           context.parsed.y.toLocaleString('es-AR', {
                             minimumFractionDigits: 2,
                             maximumFractionDigits: 2
                           });
                  }
                }
              }
            },
            scales: {
              x: {
                stacked: true,
                title: {
                  display: true,
                  text: 'Fecha de Venta',
                  font: { size: 14 }
                },
                grid: {
                  display: false
                }
              },
              y: {
                stacked: true,
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Total en Pesos ($)',
                  font: { size: 14 }
                },
                ticks: {
                  callback: function(value) {
                    return '$' + value.toLocaleString('es-AR');
                  }
                }
              }
            }
          }
        });
      }

      function getColor(index, alpha = 1) {
        const colors = [
          `rgba(255, 99, 132, ${alpha})`,
          `rgba(54, 162, 235, ${alpha})`,
          `rgba(255, 206, 86, ${alpha})`,
          `rgba(75, 192, 192, ${alpha})`,
          `rgba(153, 102, 255, ${alpha})`,
          `rgba(255, 159, 64, ${alpha})`,
          `rgba(124, 186, 59, ${alpha})`,
          `rgba(181, 109, 255, ${alpha})`
        ];
        return colors[index % colors.length];
      }

      Livewire.on('make-chart', (event) => {
        const chartData = event[0];
        initChart(chartData);
      });

      document.addEventListener('DOMContentLoaded', function() {
        initChart({
          labels: [],
          datasets: []
        });
      });

    </script>
  @endscript
</div>
