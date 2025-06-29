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
        <span class="dato">
          {{ $sale_data['header']['establecimiento']['razon_social'] }}
          <span>&nbsp;CUIT: {{ $sale_data['header']['establecimiento']['cuit'] }}</span>
          <span>&nbsp;Inicio de actividades: {{ $sale_data['header']['establecimiento']['inicio_actividades'] }}</span>
        </span>
      </p>
      <p class="renglon">
        <span class="meta">Contáctenos:</span>
        <span class="dato">
          <span>&nbsp;Tel: {{ $sale_data['header']['establecimiento']['telefono'] }} | Correo: {{ $sale_data['header']['establecimiento']['email'] }}</span>
        </span>
      </p>
      <p class="renglon">
        <span class="meta">Cliente:</span>
        <span class="dato">
          <span class="capitalize">usuario:&nbsp;{{ $sale_data['header']['cliente']['username'] }}, </span>
          <span class="capitalize">nombre completo:&nbsp;{{ $sale_data['header']['cliente']['full_name'] }}</span>
          <span> - DNI: {{ $sale_data['header']['cliente']['dni'] }} </span>
        </span>
      </p>
      <p class="renglon">
        <span class="meta">Estado de la cuenta:</span>
        <span class="dato">{{ $sale_data['header']['cliente']['account_status'] }}</span>
      </p>
      <p class="renglon">
        <span class="meta">Contacto:&nbsp;</span>
        <span class="dato">
          <span>Tel:  {{ $sale_data['header']['cliente']['contact'] }} </span>
          <span>Email: {{ $sale_data['header']['cliente']['email'] }} </span>
        </span>
      </p>
      <p class="renglon">
        <span class="meta">Dirección:&nbsp;</span>
        <span class="dato">
          <span>{{ $sale_data['header']['cliente']['full_address'] }}</span>
        </span>
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
