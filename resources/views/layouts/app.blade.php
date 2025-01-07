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

      {{-- notificaciones toast, llamadas 'toast-event' --}}
      <div x-cloak x-data="{
        showToast: false,
        toastData: null,
        toastIcon: '',
        getToastClasses() {
            const types = {
                success: 'bg-emerald-100 border-emerald-500',
                info: 'bg-blue-100 border-blue-500',
                error: 'bg-red-100 border-red-500'
            };
            return `${types[this.toastData?.event_type] || ''} `;
        },
        setToastIcon() {
            const icons = {
                success: '&#10003;',
                info: '&#33;',
                error: '&#10007;'
            };
            this.toastIcon = icons[this.toastData?.event_type] || '';
        },
        init() {
            window.addEventListener('toast-event', (event) => {
                this.toastData = event.detail.toast_data;
                this.setToastIcon();
                this.showToast = true;
                //setTimeout(() => this.showToast = false, 5000);
            });
        }
      }">
        {{-- visualizacion y posicion del toast --}}
        <div x-show="showToast"
              x-transition
              class="absolute z-50 top-32 left-2 lg:inset-x-1/3">
            <div
              :class="'relative flex gap-3 p-4 rounded-lg border max-w-lg' + ' ' + getToastClasses()">
              <div class="mt-0.5">
                <span x-text="toastIcon" class="text-xl"></span>
              </div>

              <div class="flex flex-col gap-1">
                <h3 x-text="toastData?.title_toast" class="text-sm font-medium text-neutral-800"></h3>
                <p x-text="toastData?.descr_toast" class="text-sm text-neutral-600"></p>
                <span x-on:click="showToast = false" class="absolute top-2 right-2 cursor-pointer">&#10005;</span>
              </div>
            </div>
        </div>
      </div>

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
