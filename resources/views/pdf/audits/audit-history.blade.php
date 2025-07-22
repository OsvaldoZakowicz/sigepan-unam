@extends('pdf.layout')

@section('contenido')
{{-- cabecera --}}
<header class="contenedor">
  <h3>historial de auditoria</h3>
  <small>documento interno</small>
  {{-- datos generales de auditoria --}}
  <div>
    <p class="renglon">
      <span class="meta">fechas del historial:</span>
      <span class="dato">desde:&nbsp;,&nbsp;hasta:&nbsp;.</span>
    </p>
    <p class="renglon">
      <span class="meta">tabla afectada:</span>
      <span class="dato">nombre:&nbsp;id:&nbsp;</span>
    </p>
  </div>
</header>
{{-- registro de cambios --}}
<section class="contenedor">
  <p class="renglon">
    <span class="meta">orden de visualización:</span>
    <span class="dato">ultimos cambios primero.</span>
  </p>
  <hr>
  {{-- un encabezado --}}
  <div>
    <p class="renglon">
      <span class="meta">registro de auditoria:</span>
      <span class="dato">1</span>
    </p>
    <p class="renglon">
      <span class="meta">evento:</span>
      <span class="dato">1</span>
    </p>
    <p class="renglon">
      <span class="meta">fecha del evento:</span>
      <span class="dato">1</span>
    </p>
    <p class="renglon">
      <span class="meta">responsable del cambio:</span>
      <span class="dato">usuario:</span>
    </p>
  </div>
  {{-- un registro --}}
  <table>
    <thead>
      <tr>
        <th class="td-text">propiedad afectada:</th>
        <th class="td-text">valor anterior:</th>
        <th class="td-text">valor nuevo:</th>
      </tr>
    </thead>
    <tbody>
        <tr>
          <td class="td-text">dato</td>
          <td class="td-text">dato</td>
          <td class="td-text">dato</td>
        </tr>
    </tbody>
  </table>
  <hr>
</section>
@endsection