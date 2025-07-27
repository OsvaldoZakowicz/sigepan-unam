@extends('pdf.layout')

@section('contenido')
{{-- cabecera --}}
<header class="contenedor">
  <h3>auditoria</h3>
  <small>documento interno</small>
  {{-- datos generales de auditoria --}}
  <div>
    <p class="renglon">
      <span class="meta">registro de auditoria:</span>
      <span class="dato">{{ $audit_metadata['audit_id'] }}</span>
    </p>
    <p class="renglon">
      <span class="meta">evento:</span>
      <span class="dato">{{ $event }}</span>
    </p>
    <p class="renglon">
      <span class="meta">fecha del evento:</span>
      <span class="dato">{{ Date::parse($audit_metadata['audit_created_at'])->format('d-m-Y H:i:s') }} hs.</span>
    </p>
    <p class="renglon">
      <span class="meta">IP de origen:</span>
      <span class="dato">{{ $audit_metadata['audit_ip_address'] }}</span>
    </p>
    <p class="renglon">
      <span class="meta">responsable del cambio:</span>
      <span class="dato">usuario: {{ $user_resp['name'] }}, email: {{ $user_resp['email'] }} rol: {{ $user_resp['role'] }}</span>
    </p>
    <p class="renglon">
      <span class="meta">tabla afectada:</span>
      <span class="dato">nombre:&nbsp;{{ $model['table'] }}, id:&nbsp;{{ $audit->auditable_id }}</span>
    </p>
  </div>
</header>
{{-- registro de cambios --}}
<section class="contenedor">
  <table>
    <thead>
      <tr>
        <th class="td-text">propiedad afectada:</th>
        <th class="td-text">valor nuevo:</th>
        <th class="td-text">valor anterior:</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($audit_modified_properties as $property_name => $property_changes)
        <tr>
          <td class="td-text">{{ $model['attributes'][$property_name] }}</td>
          @foreach ($property_changes as $status => $value)

            @if ($event === 'Creado')
              @if ($status === 'new')
                <td class="td-text">{{ $value }}</td>
              @endif
              <td class="td-text">-</td>
            @endif

            @if ($event === 'Actualizado')
              @if ($status === 'new')
                <td class="td-text">{{ $value }}</td>
              @elseif ($status === 'old')
                <td class="td-text">{{ $value }}</td>
              @endif
            @endif

            @if ($event === 'Eliminado')
              <td class="td-text">-</td>
              @if ($status === 'old')
                <td class="td-text">{{ $value }}</td>
              @endif
            @endif

            @if ($event === 'Restaurado')
            @if ($status === 'new')
              <td class="td-text">{{ $value }}</td>
            @endif
              <td class="td-text">-</td>
            @endif

          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</section>
@endsection