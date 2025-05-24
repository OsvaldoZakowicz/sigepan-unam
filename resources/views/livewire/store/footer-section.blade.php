<div class="flex flex-col justify-start items-start text-orange-100 text-md p-10">

  <div class="flex flex-col gap-2">
    @if (count($negocio) !== 0)
      <div class="w-1/2 flex flex-col p-1 gap-2">
        <span class="font-semibold text-orange-600">{{ $negocio['razon_social'] ?? ''}}</span>
        <span><span class="font-semibold">CUIT:&nbsp;</span>{{ $negocio['cuit'] ?? '' }}</span>
        <span><span class="font-semibold">Visitanos en:&nbsp;</span>{{ $negocio['domicilio'] ?? '' }}</span>
        <span><span class="font-semibold">Contáctanos:&nbsp;</span>Tel:&nbsp;{{ $negocio['telefono'] ?? '' }}&nbsp;|&nbsp;Email:&nbsp;{{ $negocio['email'] ?? '' }}</span>
      </div>
    @endif

    @if (count($tienda) !== 0)
    <div class="w-1/2 flex flex-col p-1 gap-2">
      <span><span class="font-semibold text-orange-400">Horario de atencion:&nbsp;</span>{{ $tienda['horario_atencion'] ?? '' }}</span>
      <span><span class="font-semibold text-orange-400">Retiro del pedido:&nbsp;</span>{{ $tienda['lugar_retiro_productos'] ?? '' }}</span>
    </div>
    @endif
  </div>

  {{-- sistema --}}
  <div class="w-1/2 flex flex-col p-1 gap-2">
    <span class="mt-10 text-xs text-orange-600">SiGePAN - Sistema de Gestión para Panaderías - {{ date('Y') }}</span>
  </div>
</div>
