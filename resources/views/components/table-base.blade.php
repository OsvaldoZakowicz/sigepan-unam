@props([
  'tablehead',
  'tablebody'
])

<table {{ $attributes->merge(
  ['class' => 'w-full table-auto border-collapse border rounded capitalize'],
) }}>
  <thead class="border text-sm font-medium">
    {{ $tablehead }}
  </thead>
  <tbody class="border text-sm font-normal">
    {{ $tablebody }}
  </tbody>
</table>
