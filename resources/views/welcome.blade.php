<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Laravel</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

  {{-- SDK MercadoPago.js --}}
  <script src="https://sdk.mercadopago.com/js/v2"></script>

  {{-- livewire --}}
  @livewireStyles()
  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- vista de bienvenida --}}
<body class="antialiased font-sans">

  <header class="">
    @if (Route::has('login'))
      {{-- navegacion de bienvenida --}}
      @livewire('welcome.navigation')
    @endif
  </header>

  {{-- tienda --}}
  @livewire('store.store')

  @php

    /* prueba de mercado pago */

    // SDK
    use MercadoPago\MercadoPagoConfig;
    use MercadoPago\Client\Preference\PreferenceClient;

    MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

    $client = new PreferenceClient();

    $preference = $client->create([
      "items"=> array(
        array(
          "title" => "Mi producto",
          "quantity" => 1,
          "unit_price" => 2000
        )
      )
    ]);

    $preference_id = $preference->id

  @endphp

  <div id="wallet_container"></div>

  {{-- livewire --}}
  @livewireScripts()

  <script>

    document.addEventListener('DOMContentLoaded', function() {
      // Verificar que todos los scripts estén cargados

      window.onload = function() {

        // El DOM y todos los recursos están cargados
        console.log('DOM y scripts cargados completamente');


        const mp = new MercadoPago('APP_USR-1175ee28-0ac9-44ff-a9fe-97fb067bf07b');
        const bricksBuilder = mp.bricks();


        mp.bricks().create("wallet", "wallet_container", {
          initialization: {
              preferenceId: "{{ $preference_id }}",
              redirectMode: "blank"
          },
          customization: {
            texts: {
              valueProp: 'smart_option',
            },
          },
        });

      };
    });


  </script>

</body>

</html>
