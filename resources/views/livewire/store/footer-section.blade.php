<div class="p-2 flex justify-start items-start text-orange-100 text-md mt-10 ml-24">
  @if (count($negocio) !== 0)
    <div class="w-1/2 flex flex-col p-1 gap-2">
      <span class="font-semibold text-orange-600">{{ $negocio['razon_social'] ?? ''}}</span>
      <span><span class="font-semibold">CUIT:&nbsp;</span>{{ $negocio['cuit'] ?? '' }}</span>
      <span><span class="font-semibold">Visitanos en:&nbsp;</span>{{ $negocio['domicilio'] ?? '' }}</span>
      <span><span class="font-semibold">Contáctanos:&nbsp;</span>Tel:&nbsp;{{ $negocio['telefono'] ?? '' }}&nbsp;|&nbsp;Email:&nbsp;{{ $negocio['email'] ?? '' }}</span>
      <span class="mt-36 text-sm text-orange-600">SiGePAN - Sistema de Gestión para Panaderías - {{ date('Y') }}</span>
    </div>
  @endif
</div>
