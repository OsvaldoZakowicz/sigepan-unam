<!DOCTYPE html>
<html lang="es">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Orden de Compra</title>

  <style>
    /* Estilos generales */
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #333;
      margin: 0;
      padding: 20px;
    }

    /* Contenedor principal */
    .order {
      width: 100%;
      border: 1px solid #ddd;
    }

    /* Cabecera de la orden */
    .order__header {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      background-color: #f9f9f9;
    }

    .order__code {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .order__date {
      margin-bottom: 10px;
    }

    .order__creation-info {
      font-size: 11px;
      margin-top: 10px;
      font-style: italic;
    }

    /* Sección de entidades */
    .order__entities,
    .order__anexo {
      padding-top: 15px;
      padding-left: 10px;
      border-bottom: 1px solid #ddd;
    }

    .order__entity {
      display: inline-block;
      width: 45%;
    }

    .order__entity-title {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .order__entity-name {
      margin-bottom: 3px;
    }

    .order__entity-cuit {
      margin-bottom: 3px;
    }

    .order__entity-contact {
      font-size: 11px;
    }

    /* Detalle de la orden */
    .order__detail-title {
      font-weight: bold;
      margin: 5px;
    }

    /* Tabla de productos */
    .order__table {
      width: 100%;
      border-collapse: collapse;
    }

    .order__table-header {
      background-color: #f2f2f2;
    }

    .order__table-header-cell {
      padding: 8px;
      text-align: left;
      border: 1px solid #ddd;
      font-weight: bold;
    }

    .order__table-cell {
      padding: 8px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .order__table-cell--numeric {
      text-align: right;
    }

    .order__table-cell--centered {
      text-align: center;
    }

    .order__table-footer {
      font-weight: bold;
    }

    /* Utilidades */
    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .text-bold {
      font-weight: bold;
    }

    .order__anexo-data {
      font-size: 10px;
    }
  </style>

</head>

<body>
  <div class="order">
    <div class="order__header">
      <div class="order__code">Código: {{ $order['code'] }}</div>
      <div class="order__date">Fecha de emision: {{ $order['date'] }}</div>
      <div class="order__creation-info">
        esta orden se creó a partir del presupuesto obtenido el: {{ $order['budget_date'] }}
      </div>
    </div>

    <div class="order__entities">
      <div class="order__entity">
        <div class="order__entity-title">EMISOR</div>
        <div class="order__entity-name">{{ $order['issuer_name'] }}</div>
        <div class="order__entity-cuit">CUIT: {{ $order['issuer_cuit'] }}</div>
        <div class="order__entity-contact">Contacto: {{ $order['issuer_email'] }} | Tel: {{ $order['issuer_phone'] }}</div>
      </div>

      <div class="order__entity">
        <div class="order__entity-title">PROVEEDOR</div>
        <div class="order__entity-name">{{ $order['provider_name'] }}</div>
        <div class="order__entity-cuit">CUIT: {{ $order['provider_cuit'] }}</div>
        <div class="order__entity-contact">Contacto: {{ $order['provider_email'] }} | Tel: {{ $order['provider_phone'] }}</div>
      </div>
    </div>

    <div class="order__detail-title">DETALLE:</div>

    <table class="order__table">
      <thead class="order__table-header">
        <tr>
          <th class="order__table-header-cell">#</th>
          <th class="order__table-header-cell">SUMINISTRO/PACK</th>
          <th class="order__table-header-cell">MARCA/TIPO/VOLUMEN</th>
          <th class="order__table-header-cell">CANTIDAD</th>
          <th class="order__table-header-cell">PRECIO UNIT.</th>
          <th class="order__table-header-cell">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order['items'] as $index => $item)
        <tr>
          <td class="order__table-cell">{{ $index + 1 }}</td>
          <td class="order__table-cell">{{ $item['item_name'] }}</td>
          <td class="order__table-cell">{{ $item['item_desc'] }}</td>
          <td class="order__table-cell order__table-cell--centered">{{ $item['item_quantity'] }}</td>
          <td class="order__table-cell order__table-cell--numeric">${{ number_format((float)$item['item_unit_price'], 2, '.', ',') }}</td>
          <td class="order__table-cell order__table-cell--numeric">${{ number_format((float)$item['item_total_price'], 2, '.', ',') }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="order__table-footer">
          <td colspan="5" class="order__table-cell text-right">Total:</td>
          <td class="order__table-cell order__table-cell--numeric">${{ number_format((float)$order['total'], 2, '.', ',') }}</td>
        </tr>
      </tfoot>
    </table>

    <div class="order__detail-title">ANEXO:</div>

    <div class="order__anexo">
      <p class="order__anexo-data">
        <span class="text-bold">METODO DE ENTREGA:&nbsp;</span>
        <span class="">{{ implode(', ', $anexo['delivery_type']) }}</span>
      </p>
      <p class="order__anexo-data">
        <span class="text-bold">FECHA TENTATIVA DE ENTREGA A PARTIR DE:&nbsp;</span>
        <span class="">{{ $anexo['delivery_date'] }}</span>
      </p>
      <p class="order__anexo-data">
        <span class="text-bold">METODOS DE PAGO:&nbsp;</span>
        <span class="">{{ implode(', ', $anexo['payment_method']) }}</span>
      </p>
    </div>

  </div>
</body>

</html>
