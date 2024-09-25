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
  <div class="min-h-screen bg-gray-100">
    {{-- navegacion de livewire --}}
    <livewire:layout.navigation />

    {{-- header de seccion --}}
    @if (isset($header))
      <header class="mx-auto px-4 sm:px-6 lg:px-8 py-2 bg-white shadow text-md font-medium capitalize text-neutral-700">
        <div class="w-full flex items-center justify-between h-8 px-1">
          {{ $header }}
        </div>
      </header>
    @endif

    {{-- contendio de seccion --}}
    <main class="w-full mb-2 flex flex-col items-center text-neutral-700">
      {{ $slot }}
    </main>
  </div>

  {{-- livewire --}}
  @livewireScripts()
</body>

</html>
