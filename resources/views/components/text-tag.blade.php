@props([
'color' => 'neutral'
])

@php
$colorClasses = [
'neutral' => 'bg-neutral-200 text-neutral-800',
'emerald' => 'bg-emerald-200 text-emerald-800',
'red' => 'bg-red-200 text-red-800',
];
$classes = $colorClasses[$color] ?? $colorClasses['neutral'];
@endphp

<span {{ $attributes->merge([
  'class' => $classes . ' text-sm font-normal px-2.5 py-0.5 rounded-md'
  ]) }}>
  {{ $slot ?? 'text' }}
</span>