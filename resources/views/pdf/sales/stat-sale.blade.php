@extends('pdf.layout')

@section('contenido')
  {{-- cabecera --}}
  <header class="contenedor">
    <h3>Estadística de ventas</h3>
    <small>documento interno</small>
    <div class="text-area">
      <p class="renglon">
        <span class="meta">fecha de emision:</span>
        <span class="dato">{{ $fecha }} hrs.</span>
      </p>
      <p class="renglon">
        <span class="meta">detalle:</span>
        <span class="dato">
          <span>ventas:&nbsp;desde la fecha:&nbsp;{{ $parametros['desde'] }},&nbsp;</span>
          <span>hasta la fecha:&nbsp;{{ $parametros['hasta'] }},&nbsp;</span>
          <span>de los productos:&nbsp;{{ $parametros['producto'] }}.</span>
        </span>
      </p>
    </div>
  </header>
  {{-- contenido --}}
  <section class="contenedor">
    <table>
      <thead>
        <tr>
          <th class="td-number">#</th>
          <th class="td-text">producto</th>
          <th class="td-number">fecha de venta</th>
          <th class="td-number">cantidad vendida</th>
          <th class="td-number">$total vendido</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($sales as $key => $sale)
          <tr>
            <td class="td-number">{{ $key+1 }}</td>
            <td class="td-text">{{ $sale['product'] }}</td>
            <td class="td-number">{{ $sale['date'] }}</td>
            <td class="td-number">{{ $sale['quantity_sold'] }}</td>
            <td class="td-number">${{ toMoneyFormat($sale['total']) }}</td>
          </tr>
          @if ($loop->last)
            <tr>
              <td class="td-number" colspan="4">$TOTAL:</td>
              <td class="td-number">${{ toMoneyFormat($total) }}</td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  </section>
  {{-- grafico --}}
  <div class="contenedor">
    <p class="renglon">
      <span class="meta">Grafico de Ventas por Producto y Fecha</span>
    </p>
    <div>
      <img src="{{ $chart_image_url }}" alt="Gráfico de Ventas" width="100%" height="400px">
    </div>
  </div>
@endsection