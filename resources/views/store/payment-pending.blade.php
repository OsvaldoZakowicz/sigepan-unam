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

    <article class="space-y-6 max-w-xs bg-white rounded-md p-8 shadow-lg capitalize text-center">
      <svg class="w-24 h-24 mx-auto text-amber-500" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4"/>
        <path d="M50 25V60" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
        <circle cx="50" cy="75" r="3" fill="currentColor"/>
      </svg>
      <h1 class="text-neutral-800 text-2xl">¡Tu pago está pendiente!</h1>
      <p class="text-neutral-600 font-semibold text-lg">verifica el estado del pago que haz realizado</p>
      <p class="text-neutral-600 font-semibold text-lg">gracias por tu pedido</p>
      <div class="w-full flex justify-center items-center">
        <a
          href="{{ route('store-store-orders-list') }}"
          class="inline-flex justify-between items-center mt-auto py-2 px-4 rounded border-2 border-orange-950 bg-orange-700 text-white"
          >ver mis pedidos
        </a>
      </div>
    </article>

  </div>

  @livewireScripts()
</body>
