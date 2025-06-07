<div>
  {{-- componente estadisticas de venta --}}
  {{-- componente listar productos --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="estadisticas de ventas">
    </x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header>
        {{-- busqueda --}}
        <div class="flex gap-1 justify-start items-start grow">
          {{-- fecha inicio --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="start_date">ventas desde fecha:</label>
            <input
              type="date"
              name="start_date"
              wire:model.live="start_date"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
          {{-- fecha fin --}}
          <div class="flex flex-col justify-end w-1/4">
            <label for="end_date">ventas hasta fecha:</label>
            <input
              type="date"
              name="end_date"
              wire:model.live="end_date"
              placeholder="ingrese un id, o termino de busqueda"
              class="text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"
            />
          </div>
        </div>
        {{-- limpiar campos de busqueda --}}
        <div class="flex flex-col self-start h-full">
          <x-a-button
            href="#"
            bg_color="neutral-200"
            border_color="neutral-300"
            text_color="neutral-600"
            >limpiar
          </x-a-button>
        </div>
      </x-slot:header>

      <x-slot:content>
        <div class="w-full flex flex-col gap-4">
          {{-- contenedor del grafico --}}
          <div class="flex justify-start items-center w-1/2">
            {{-- grafico --}}
            <div class="flex justify-center items-center border border-neutral-300 rounded-sm p-2" style="position: relative; height:70vh; width:100vw">
              <canvas id="sales_chart"></canvas>
            </div>
          </div>
          {{-- tabla de datos --}}
          <x-table-base>
            <x-slot:tablehead>
                <tr class="border bg-neutral-100">
                    <x-table-th class="text-end">#</x-table-th>
                    <x-table-th class="text-start">fecha</x-table-th>
                    <x-table-th class="text-start">producto</x-table-th>
                    <x-table-th class="text-end">$total</x-table-th>
                </tr>
            </x-slot:tablehead>
            <x-slot:tablebody>
                @forelse ($sales as $key => $sale)
                    @foreach ($sale['products'] as $product => $total)
                      <tr class="border">
                        <x-table-td class="text-end">
                          {{ $loop->index+1 }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $sale['date'] }}
                        </x-table-td>
                        <x-table-td class="text-start">
                          {{ $product }}
                        </x-table-td>
                        <x-table-td class="text-end">
                          ${{ number_format($total, 2) }}
                        </x-table-td>
                      </tr>
                    @endforeach
                @empty
                  <tr class="border">
                    <x-table-td colspan="4">
                      ¡sin registros!
                    </x-table-td>
                  </tr>
                @endforelse
            </x-slot:tablebody>
          </x-table-base>

        </div>
      </x-slot:content>

      <x-slot:footer class="">
      </x-slot:footer>

    </x-content-section>
  </article>

  @script
    <script>

      const ctx = document.getElementById('sales_chart');
      let chart;

      function initChart(dates, datasets) {
        if (chart) {
          chart.destroy();
        }

        chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: dates,
            datasets: datasets.map((dataset, index) => ({
              label: dataset.label,
              data: dataset.data,
              borderWidth: 1,
              stack: 'stack1', // Esto hace que las barras se apilen
              backgroundColor: getColor(index) // Función para generar colores diferentes
            }))
          },
          options: {
            responsive: true,
            autoPadding: true,
            scales: {
                y: {
                  stacked: true, // Habilitar apilamiento
                  title: {
                    display: true,
                    text: 'Total en $',
                    font: { size: 14 }
                  },
                  ticks: {
                    callback: value => '$ ' + value.toLocaleString('es-AR')
                  }
                },
                x: {
                  stacked: true, // Habilitar apilamiento
                  title: {
                    display: true,
                    text: 'Fecha de venta',
                    font: { size: 14 }
                  }
                }
            }
          }
        });
      }

      // Función para generar colores
      function getColor(index) {
        const colors = [
          '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
          '#9966FF', '#FF9F40', '#7CBA3B', '#B56DFF'
        ];
        return colors[index % colors.length];
      }

      Livewire.on('make-chart', (event) => {
        initChart(event[0].dates, event[0].datasets);
      });

    </script>
  @endscript
</div>
