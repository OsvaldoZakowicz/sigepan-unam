<div class="p-1 text-orange-100 font-semibold text-2xl capitalize">
  @if (count($negocio) !== 0)
    <span>{{ $negocio['nombre_comercial'] ?? 'panaderia' }}</span>
  @endif
</div>
