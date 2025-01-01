@props(['type' => 'success', 'msg' => ''])

@php

  /* estilos segun el tipo */
  $styles = [
    'success' => [
      'bg' => 'bg-emerald-50',
      'border' => 'border-emerald-200',
      'icon' => 'text-emerald-500',
      'hover' => 'hover:text-emerald-700',
      'symbol' => '&#10003;'
    ],
    'info' => [
      'bg' => 'bg-blue-50',
      'border' => 'border-blue-200',
      'icon' => 'text-blue-500',
      'hover' => 'hover:text-blue-700',
      'symbol' => '&#33;'
    ],
    'error' => [
      'bg' => 'bg-red-50',
      'border' => 'border-red-200',
      'icon' => 'text-red-500',
      'hover' => 'hover:text-red-700',
      'symbol' => '&#10007;'
    ]
  ][$type];

  /* titulo segun el tipo */
  $title = [
    'success' => 'Operación exitosa',
    'info' => 'Información',
    'error' => 'Error'
  ][$type];

@endphp

<div
  x-data="{ open: true }"
  x-cloak
  x-show="open"
  x-on:click.outside="open = false"
  class="absolute z-50 top-32 left-2 lg:inset-x-1/3"
  >
  <div class="relative flex gap-3 p-4 rounded-lg border {{ $styles['bg'] }} {{ $styles['border'] }} max-w-lg">
    <div class="mt-0.5">
      <span class="text-xl {{ $styles['icon'] }}">{!! $styles['symbol'] !!}</span>
    </div>

    <div class="flex flex-col gap-1">
      <span class="text-sm font-medium text-neutral-800">{{ $title }}</span>
      <span class="text-sm text-neutral-600">{{ $msg }}</span>
      <span x-on:click="open = false" class="absolute top-2 right-2 {{ $styles['icon'] }} {{ $styles['hover'] }} cursor-pointer">&#10005;</span>
    </div>
  </div>
</div>
