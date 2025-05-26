<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Laravel</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

  {{-- livewire --}}
  @livewireStyles()

  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900">

  <div class="w-full h-screen flex justify-center items-center">

    <article class="space-y-6 bg-white rounded-md p-8 shadow-lg capitalize text-center">
      <svg class="w-24 h-24 mx-auto text-red-500" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4"/>
        <path d="M35 35L65 65M65 35L35 65" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <h1 class="text-neutral-800 text-2xl">¡No realizó el pago!</h1>
      <p class="text-neutral-600 font-semibold text-lg">el pago del pedido aún esta pendiente. Puede cerrar esta pestaña.</p>
      @if ($datos_tienda_pago)
        <small class="text-neutral-600">{{ $datos_tienda_pago }}</small>
      @endif
    </article>

  </div>

  @livewireScripts()
</body>
