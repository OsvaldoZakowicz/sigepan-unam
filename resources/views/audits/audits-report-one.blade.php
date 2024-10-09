<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Reporte de auditoria</title>
  {{-- css --}}
  <style>

    * {
      margin: 0;
      padding: 0;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      box-sizing: border-box;
    }

    body {
      font-family: 'Open Sans', sans-serif;
      line-height: 1.5;
      padding: 2rem;
    }

    header,
    main {
      width: 100%;
    }

    table {
      width: 100%;
      text-align: left;
      border-collapse: collapse;
    }

    tr {
      border-bottom: 1px solid;
    }

    th,
    td {
      border: 1px solid;
      padding: 5px;
      vertical-align: text-top;
    }

    th {
      text-align: start;
    }

    /* thead th:not(:first-child),
    td {
      text-align: end;
    } */

    h1 {
      font-size: 2rem;
      color: #333333;
      margin-top: 1rem;
      margin-bottom: 0.5rem;
    }

    h4 {
      font-size: 1.1rem;
      color: #6e6e6e;
      margin-top: 1rem;
      margin-bottom: 0.5rem;
    }

    ul {
      list-style-position: inside;
    }

    .date-text {
      font-weight: 600;
      font-size: 1rem;
    }

    .auditor,
    .auditor-metadata {
      font-weight: 600;
    }

    .auditor-data {
      font-weight: 400;
      font-style: italic;
      text-transform: capitalize;
    }

    .new-value,
    .old-value {
      display: block;
      width: 100%;
    }

    .metadata {
      font-weight: 600;
    }

    .data {
      font-weight: 400;
      font-style: italic;
    }

  </style>
</head>

<body>
  {{-- cabecera --}}
  <header>
    {{-- todo: helper para obtener la fecha y hora actual --}}
    <p class="date-text">fecha de emisión:&nbsp;<span>fecha.</span></p>
    <h1>Reporte de auditoría:</h1>
    <p class="auditor">Auditor:&nbsp;</p>
    <ul>
      <li class="auditor-metadata">Nombre y Apellido:&nbsp;
        <span class="auditor-data">
          @if ($auditor->profile)
            {{-- tiene perfil --}}
            <span>{{ $auditor->profile->first_name }}&nbsp;{{ $auditor->profile->last_name }}</span>
          @else
            {{-- no tiene perfil --}}
            <span>DEBE COMPLETAR SU PERFIL!</span>
          @endif
        </span>
      </li>
      <li class="auditor-metadata">Usuario:&nbsp;<span class="auditor-data">{{ $auditor->name }}</span></li>
      <li class="auditor-metadata">Email:&nbsp;<span class="auditor-data">{{ $auditor->email }}</span></li>
      <li class="auditor-metadata">Rol:&nbsp;<span class="auditor-data">{{ $auditor->getRoleNames()->first()}}</span></li>
    </ul>
    <h4>Detalle de auditoría: registro individual.</h4>
    {{-- tabla de detalle --}}
    {{-- todo: traducir el evento al español --}}
    <table>
      <thead>
        <tr>
          <th scope="column">campo de auditoria:</th>
          <th scope="column">valor.</th>
        </tr>
      </thead>
      <tbody>
        {{-- metadatos de auditoria --}}
        <tr>
          <th scope="row" class="metadata">Registro de auditoria N°:</th>
          <td class="data">{{ $audit_metadata['audit_id'] }}</td>
        </tr>
        <tr>
          <th scope="row">Evento registrado:</th>
          <td class="data">{{ $audit_metadata['audit_event'] }}</td>
        </tr>
        <tr>
          <th scope="row" class="metadata">Fecha del evento:</th>
          <td class="data">{{ Date::parse($audit_metadata['audit_created_at'])->format('d-m-Y H:i:s') }}&nbsp;hrs.</td>
        </tr>
        <tr>
          <th scope="row" class="metadata">IP de origen:</th>
          <td class="data">{{ $audit_metadata['audit_ip_address'] }}</td>
        </tr>
        <tr>
          <th scope="row" class="metadata">Responsable del cambio:</th>
          <td class="data">usuario:&nbsp;{{ $audit_metadata['user_name'] }},&nbsp;email:&nbsp;{{ $audit_metadata['user_email'] }}</td>
        </tr>
        <tr>
          <th scope="row" class="metadata">Tabla modificada:</th>
          <td class="data">{{ englishPluralFromPath($audit->auditable_type) }}</td>
        </tr>
        <tr>
          <th scope="row" class="metadata">Registro de tabla:</th>
          <td class="data">{{ $audit->auditable_id }}</td>
        </tr>
      </tbody>
    </table>
  </header>
  {{-- cuerpo --}}
  <main>
    <h4>Registro de cambios:</h4>
    <table>
      <thead></thead>
      <tbody>
        @foreach ($audit_modified_properties as $property_name => $property_changes)
          <tr>
            <th>
              {{-- todo: traducir nombres de propiedades al español --}}
              <span class="metadata">propiedad afectada:</span>
              <span class="data">{{ $property_name }}</span>
            </th>
            <td>
              @foreach ($property_changes as $status => $value)
                @if ($status === 'new')
                  <span class="new-value">
                    <span class="metadata">&plus;&nbsp;valor nuevo:</span>
                    <span class="data">{{ $value }}</span>
                  </span>
                @else
                  <span class="old-value">
                    <span class="metadata">&minus;&nbsp;valor anterior:</span>
                    <span class="data">{{ $value }}</span>
                  </span>
                @endif
              @endforeach
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </main>
</body>

</html>
