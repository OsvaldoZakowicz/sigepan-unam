@extends('pdf.layout')

@section('contenido')
  {{-- cabecera --}}
<header class="contenedor">
  <h3>presupuesto</h3>
  <small>documento no valido como factura - </small>
  <small>comprobante interno</small>
  {{-- datos generales de orden --}}
  <div>
    <p class="renglon">
      <span class="meta">código de presupuesto:</span>
      <span class="dato">{{ $quotation_data['quotation_code'] }}</span>
    </p>
    <p class="renglon">
      <span class="meta">fecha de presupuesto:</span>
      <span class="dato">{{ $quotation_data['quotation_date'] }}</span>
    </p>
  </div>
  <hr>
  <table class="no-borders">
    <tbody>
      <tr>
        <td class="td-text" style="vertical-align: start;">
          <p class="renglon">DE:</p>
          <p class="renglon">
            <span class="meta" style="text-transform: capitalize;">proveedor:</span>
            <span class="dato">{{ $quotation_data['provider_name'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">CUIT:</span>
            <span class="dato">{{ $quotation_data['provider_cuit'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">teléfono:</span>
            <span class="dato">{{ $quotation_data['provider_phone'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">correo:</span>
            <span class="dato">{{ $quotation_data['provider_email'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">direccion:</span>
            <span class="dato">{{ $quotation_data['provider_address'] }}</span>
          </p>
        </td>
        <td class="td-text" style="vertical-align: start;">
          <p class="renglon">PARA:</p>
          <p class="renglon">
            <span class="meta" style="text-transform: capitalize;">panadería:</span>
            <span class="dato">{{ $quotation_data['issuer_name'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">CUIT:</span>
            <span class="dato">{{ $quotation_data['issuer_cuit'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">inicio de actividades:</span>
            <span class="dato">{{ $quotation_data['issuer_start'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">teléfono:</span>
            <span class="dato">{{ $quotation_data['issuer_phone'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">correo:</span>
            <span class="dato">{{ $quotation_data['issuer_email'] }}</span>
          </p>
          <p class="renglon">
            <span class="meta">direccion:</span>
            <span class="dato">{{ $quotation_data['issuer_address'] }}</span>
          </p>
        </td>
      </tr>
    </tbody>
  </table>
</header>
{{-- contenido --}}
<section class="contenedor">
  {{-- datos --}}
  <table>
    <thead>
      <tr>
        <th class="td-number">#</th>
        <th class="td-text">suministro/pack</th>
        <th class="td-text">marca/volumen</th>
        <th class="td-number">cantidad presupuestada</th>
        <th class="td-number">$precio unitario</th>
        <th class="td-number">$subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($quotation_data['items'] as $key => $item )
      <tr>
        <td class="td-number">{{ $key+1 }}</td>
        <td class="td-text">{{ $item['item_name'] }}</td>
        <td class="td-text">{{ $item['item_desc'] }}</td>
        <td class="td-number">{{ $item['item_quantity'] }}</td>
        <td class="td-number">${{ toMoneyFormat($item['item_unit_price']) }}</td>
        <td class="td-number">${{ toMoneyFormat($item['item_total_price']) }}</td>
      </tr>
      @endforeach
      <tr>
        <td class="td-number" colspan="5">$TOTAL:</td>
        <td class="td-number">${{ toMoneyFormat($quotation_data['total']) }}</td>
      </tr>
    </tbody>
  </table>
</section>
@endsection