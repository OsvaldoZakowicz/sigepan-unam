@extends('pdf.layout')

@section('contenido')
    {{-- cabecera --}}
    <header class="contenedor">
        <h3>orden de compra</h3>
        <small>documento no valido como factura - </small>
        <small>comprobante interno</small>
        {{-- datos generales de orden --}}
        <div>
            <p class="renglon">
                <span class="meta">código de orden:</span>
                <span class="dato">{{ $order['order_code'] }}</span>
            </p>
            <p class="renglon">
                <span class="meta">fecha de orden:</span>
                <span class="dato">{{ $order['order_date'] }}</span>
            </p>
            <p class="renglon">
                <span class="meta">presupuesto previo:</span>
                @php
                    $msg = $order['budget_code']
                        ? 'presupuesto: ' . $order['budget_code'] . ' fecha: ' . $order['budget_date']
                        : 'sin presupuesto previo';
                @endphp
                <span class="dato">{{ $msg }}</span>
            </p>
        </div>
        <hr>
        <table class="no-borders">
            <tbody>
                <tr>
                    <td class="td-text" style="vertical-align: start;">
                        <p class="renglon">
                            <span class="meta" style="text-transform: capitalize;">panadería:</span>
                            <span class="dato">{{ $order['issuer_name'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">CUIT:</span>
                            <span class="dato">{{ $order['issuer_cuit'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">inicio de actividades:</span>
                            <span class="dato">{{ $order['issuer_start'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">teléfono:</span>
                            <span class="dato">{{ $order['issuer_phone'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">correo:</span>
                            <span class="dato">{{ $order['issuer_email'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">direccion:</span>
                            <span class="dato">{{ $order['issuer_address'] }}</span>
                        </p>
                    </td>
                    <td class="td-text" style="vertical-align: start;">
                        <p class="renglon">
                            <span class="meta" style="text-transform: capitalize;">proveedor:</span>
                            <span class="dato">{{ $order['provider_name'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">CUIT:</span>
                            <span class="dato">{{ $order['provider_cuit'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">teléfono:</span>
                            <span class="dato">{{ $order['provider_phone'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">correo:</span>
                            <span class="dato">{{ $order['provider_email'] }}</span>
                        </p>
                        <p class="renglon">
                            <span class="meta">direccion:</span>
                            <span class="dato">{{ $order['provider_address'] }}</span>
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
                    <th class="td-text">tipo/marca/volumen</th>
                    <th class="td-number">cantidad</th>
                    <th class="td-number">$precio unitario</th>
                    <th class="td-number">$subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order['items'] as $key => $item )
                <tr>
                    <td class="td-number">{{ $key+1 }}</td>
                    <td class="td-text">{{ $item['item_name'] }}</td>
                    <td class="td-text">{{ $item['item_type'] }} {{ $item['item_desc'] }}</td>
                    <td class="td-number">{{ $item['item_quantity'] }}</td>
                    <td class="td-number">${{ toMoneyFormat($item['item_unit_price']) }}</td>
                    <td class="td-number">${{ toMoneyFormat($item['item_total_price']) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="td-number" colspan="5">$TOTAL:</td>
                    <td class="td-number">${{ toMoneyFormat($order['total']) }}</td>
                </tr>
            </tbody>
        </table>
        <hr>
        {{-- anexo --}}
        <div>
            <p class="renglon">
                <span class="meta">tipo de entrega:</span>
                <span class="dato">
                  @foreach ($anexo['delivery_type'] as $dt)
                    <span>{{ $dt }},&nbsp;</span>
                  @endforeach
                </span>
            </p>
            <p class="renglon">
                <span class="meta">fecha tentativa de entrega o retiro a partir de:</span>
                <span class="dato">{{ $anexo['delivery_date'] }}</span>
            </p>
            <p class="renglon">
                <span class="meta">métodos de pago aceptados:</span>
                <span class="dato">
                  @foreach ($anexo['payment_method'] as $pm)
                    <span>{{ $pm }},&nbsp;</span>
                  @endforeach
                </span>
            </p>
            <p class="renglon">
                <span class="meta">el provedor aceptó los terminos?:</span>
                <span class="dato">{{ ($anexo['accept_terms']) ? 'si' : 'no' }}</span>
            </p>
            <p class="renglon">
                <span class="meta">comentarios:</span>
                <span class="dato">{{ ($anexo['short_description']) ? $anexo['short_description'] : 'ninguno' }}</span>
            </p>
        </div>
    </section>
@endsection
