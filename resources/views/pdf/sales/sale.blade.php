@extends('pdf.layout')

@section('contenido')
  {{-- cabecera --}}
  <header class="contenedor">
    <h3>comprobante de venta</h3>
    <small>documento no valido como factura</small>
    <div class="text-area">
      <p class="renglon">
        <span class="meta">Id de venta:</span>
        <span class="dato">{{ $sale_data['header']['id'] }}</span>
      </p>
      <p class="renglon">
        <span class="meta">Fecha:</span>
        <span class="dato">{{ $sale_data['header']['fecha'] }} hs.</span>
      </p>
      <p class="renglon">
        <span class="meta">Establecimiento:</span>
        <span class="dato"></span>
      </p>
      <p class="renglon">
        <span class="meta">Cliente:</span>
        <span class="dato">{{ $sale_data['header']['cliente'] }}</span>
      </p>
      <p class="renglon">
        <span class="meta">Forma de pago:</span>
        <span class="dato">{{ $sale_data['header']['forma_de_pago'] }}</span>
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
          <th class="td-text">detalle</th>
          <th class="td-number">cantidad</th>
          <th class="td-number">$precio unitario</th>
          <th class="td-number">$subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($sale_data['detail'] as $detail)
          <tr>
            <td class="td-number">{{ $detail['nro'] }}</td>
            <td class="td-text">{{ $detail['producto'] }}</td>
            <td class="td-text">{{ $detail['detalle'] }}</td>
            <td class="td-number">{{ $detail['cantidad'] }}</td>
            <td class="td-number">${{ $detail['precio_unitario'] }}</td>
            <td class="td-number">${{ $detail['subtotal'] }}</td>
          </tr>
        @endforeach
        <tr>
          <td class="td-number" colspan="5">$TOTAL:</td>
          <td class="td-number">${{ $sale_data['header']['total'] }}</td>
        </tr>
      </tbody>
    </table>
  </section>
@endsection
