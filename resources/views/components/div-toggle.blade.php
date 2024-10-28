@props([
  'title' => 'grupo',
  'subtitle' => '',
  'messages' => ''
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
    <span class="font-semibold text-sm capitalize">{{ $title }}</span>
  </legend>

  <span>{{ $subtitle }}</span>

  {{-- mensajes en la seccion --}}
  {{-- $mensajes es un slot con nombre --}}
  <span class="mx-2">
    {{ $messages }}
  </span>

  {{-- content --}}
  <div x-show="open" class="w-full bg-white pt-2">
    {{ $slot }}
  </div>

</fieldset>
