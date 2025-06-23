<?php

namespace App\Services\Charts;

class ChartService
{
  /**
   * Genera URL del grafico usando QuickChart.io
   */
  public function generateChartUrl(array $chartData, int $width = 800, int $height = 400): string
  {
    // Validar que hay datos
    if (empty($chartData['labels']) || empty($chartData['datasets'])) {
      return $this->getNoDataChartUrl($width, $height);
    }

    $config = [
      'type' => 'bar',
      'data' => [
        'labels' => $chartData['labels'],
        'datasets' => $this->formatDatasets($chartData['datasets'])
      ],
      'options' => [
        'responsive' => false,
        'plugins' => [
          'title' => [
            'display' => true,
            'text' => 'Ventas por Producto y Fecha',
            'font' => ['size' => 14]
          ],
          'legend' => [
            'display' => true,
            'position' => 'top'
          ]
        ],
        'scales' => [
          'x' => [
            'stacked' => true,
            'title' => [
              'display' => true,
              'text' => 'Fecha de Venta'
            ],
            'grid' => ['display' => false]
          ],
          'y' => [
            'stacked' => true,
            'beginAtZero' => true,
            'title' => [
              'display' => true,
              'text' => 'Total en Pesos ($)'
            ]
          ]
        ]
      ]
    ];

    return $this->buildQuickChartUrl($config, $width, $height);
  }

  /**
   * Formatea los datasets para QuickChart
   */
  private function formatDatasets(array $datasets): array
  {
    return array_map(function ($dataset, $index) {
      return [
        'label' => $dataset['label'],
        'data' => $dataset['data'],
        'backgroundColor' => $this->getColor($index, 0.7),
        'borderColor' => $this->getColor($index, 1),
        'borderWidth' => 1,
        'stack' => 'stack1'
      ];
    }, $datasets, array_keys($datasets));
  }

  /**
   * Construye la URL de QuickChart
   */
  private function buildQuickChartUrl(array $config, int $width, int $height): string
  {
    $encodedConfig = urlencode(json_encode($config));
    return "https://quickchart.io/chart?c={$encodedConfig}&width={$width}&height={$height}&format=png&backgroundColor=white";
  }

  /**
   * URL para gráfico cuando no hay datos
   */
  private function getNoDataChartUrl(int $width, int $height): string
  {
    $config = [
      'type' => 'bar',
      'data' => [
        'labels' => ['Sin datos'],
        'datasets' => [[
          'label' => 'No hay información',
          'data' => [0],
          'backgroundColor' => 'rgba(200, 200, 200, 0.5)'
        ]]
      ],
      'options' => [
        'responsive' => false,
        'plugins' => [
          'title' => [
            'display' => true,
            'text' => 'Sin datos para mostrar',
            'font' => ['size' => 16]
          ],
          'legend' => ['display' => false]
        ]
      ]
    ];

    return $this->buildQuickChartUrl($config, $width, $height);
  }

  /**
   * Obtiene colores para el gráfico
   */
  private function getColor(int $index, float $alpha = 1): string
  {
    $baseColors = [
      [255, 99, 132],   // Rojo
      [54, 162, 235],   // Azul
      [255, 206, 86],   // Amarillo
      [75, 192, 192],   // Verde azulado
      [153, 102, 255],  // Púrpura
      [255, 159, 64],   // Naranja
      [124, 186, 59],   // Verde
      [181, 109, 255]   // Lavanda
    ];

    $color = $baseColors[$index % count($baseColors)];
    return "rgba({$color[0]}, {$color[1]}, {$color[2]}, {$alpha})";
  }
}
