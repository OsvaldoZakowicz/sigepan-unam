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

    {{-- grupo de navegacion, encabezado y notificaciones --}}
    <div class="fixed z-50 top-0 left-0 right-0">

      {{-- navegacion de livewire --}}
      <livewire:layout.navigation />

      {{-- header de seccion --}}
      @if (isset($header))
        <header class="w-full px-8 h-10 sm:px-6 lg:px-8 flex justify-start items-center bg-white shadow">
          {{ $header }}
        </header>
      @endif

      {{-- todo: componente de notificaciones por eventos --}}
      <div x-data="{}">

      </div>

      {{-- todo: notificaciones de sesion --}}
      {{-- notificaciones recibidas a traves de la sesion --}}
      <div>
        {{-- mensaje toast exito, recibido por session --}}
        @if (session('operation-success'))
          <x-session-toast type="success" msg="{{ session('operation-success') }}" />
        @endif

        {{-- mensaje toast info, recibido por sesion --}}
        @if (session('operation-info'))
          <x-session-toast type="info" msg="{{ session('operation-info') }}" />
        @endif

        {{-- mensaje toast error, recibido por sesion --}}
        @if (session('operation-error'))
          <x-session-toast type="error" msg="{{ session('operation-error') }}" />
        @endif
      </div>


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
