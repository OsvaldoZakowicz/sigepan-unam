<nav class="bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 py-8 px-6 flex flex-1 justify-end items-center gap-8">

  {{-- * navegacion de la vista bienvenida --}}
  {{-- * una vez autenticados, mostrar segun rol dashboard o tienda --}}
  @auth

    <span class="text-orange-100 capitalize italic font-semibold">
      bienvenido {{ auth()->user()->name }}, {{ auth()->user()->roles->first()->name }}
    </span>

    <span class="border-l-2 border-orange-100 h-6"></span>

    @can('panel')
      <a wire:navigate href="{{ url('/dashboard') }}" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">
        {{__('Dashboard')}}
      </a>
    @endcan

    @can('tienda')
      <x-nav-link-store
        :href="route('store-store-index')"
        :active="request()->routeIs('store-*')"
        wire:navigate
        >tienda
      </x-nav-link-store>
    @endcan

    @can('tienda-perfil')
      <a wire:navigate href="#" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">
        <span>mi perfil</span>
      </a>
    @endcan

    {{-- logout manual del cliente --}}
    @can('tienda')
      <form action="{{ route('client-logout') }}">
        @csrf
        <button type="submit" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">cerrar sesión</button>
      </form>
    @endcan

  @else

    {{-- * nadie autenticado, mostrar login y register --}}

    <a wire:navigate href="{{ route('login') }}" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">
      {{__('Log in')}}
    </a>

    @if (Route::has('register'))
      <a wire:navigate href="{{ route('register') }}" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">
        {{__('Register')}}
      </a>
    @endif

  @endauth
</nav>
