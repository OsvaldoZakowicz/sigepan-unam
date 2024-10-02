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

{{-- vista de bienvenida --}}
<body class="antialiased font-sans">
  <div class="bg-neutral-200 px-2">
    <header class="">
      @if (Route::has('login'))
        {{-- navegacion de bienvenida --}}
        <livewire:welcome.navigation />
      @endif
    </header>
  </div>
  {{-- ?podria poner toda la tienda en esta vista? --}}
  {{-- !no olvidar los permisos a las acciones --}}
  <p>Vista de bienvenida, y tienda para cliente</p>

  {{-- livewire --}}
  @livewireScripts()
</body>

</html>
