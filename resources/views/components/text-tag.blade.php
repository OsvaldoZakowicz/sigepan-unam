@props([
  'color' => 'neutral'
])

<span {{ $attributes->merge(
    [
      'class' => 'bg-'.$color.'-200 text-'.$color.'-800 text-sm font-normal px-2.5 py-0.5 rounded-md'
    ]
  ) }}>
  {{ $slot ?? 'text' }}
</span>
