@props([
  'tema' => ''
])

<fieldset {{ $attributes->merge([
  'class' => 'flex flex-wrap mb-2 border rounded border-neutral-300',
]) }}>
  <legend class="font-semibold">{{ $tema }}</legend>
  {{ $slot }}
</fieldset>
