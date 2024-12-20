<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  {{-- livewire --}}
  @livewireStyles()
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- layout raiz --}}

<body class="font-sans antialiased">
  <div class="relative min-h-screen bg-gray-100">

    <div class="fixed z-50 top-0 left-0 right-0">
      {{-- navegacion de livewire --}}
      <livewire:layout.navigation />

      {{-- header de seccion --}}
      @if (isset($header))
        <header class="w-full px-8 h-10 sm:px-6 lg:px-8 flex justify-start items-center bg-white shadow">
          {{ $header }}
        </header>
      @endif
    </div>

    {{-- contendio de seccion --}}
    <main class="w-full mb-2 flex flex-col items-center text-neutral-700 pt-28">
      {{ $slot }}
    </main>
  </div>

  {{-- livewire --}}
  @livewireScripts()
</body>

</html>
