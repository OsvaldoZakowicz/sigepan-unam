@props([
  'title' => 'grupo',
  'subtitle' => ''
])

{{-- fieldset con accion de toggle --}}
{{-- incluir siempre el x-data con open: false|true --}}
<fieldset {{ $attributes->merge([ 'class' => 'flex flex-wrap mb-2 border rounded border-neutral-300']) }}>

  {{-- titulo, y opciones de abrir o cerrar --}}
  <legend>
    {{-- marca abrir --}}
    <span
      x-on:click="open = ! open"
      x-show="!open"
      class="cursor-pointer text-lg"
      title="abrir pestaña">&nbsp;▷
      <span class="text-xs uppercase">mostrar</span>
    </span>

    {{-- marca cerrar --}}
    <span
      x-on:click="open = ! open"
      x-show="open"
      class="cursor-pointer text-lg"
      title="cerrar pestaña">&nbsp;▽
      <span class="text-xs uppercase">ocultar</span>
    </span>

    {{-- titulo o tema del grupo --}}
    <span class="font-semibold">{{ $title }}</span>
  </legend>

  <span>{{ $subtitle }}</span>

  {{-- content --}}
  <div x-show="open" class="w-full bg-white pt-2">
    {{ $slot }}
  </div>

</fieldset>
