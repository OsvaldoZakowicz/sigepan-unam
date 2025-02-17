<nav class="bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 py-8 px-6 flex flex-1 justify-end gap-8">

  {{-- * navegacion de la vista bienvenida --}}
  {{-- * una vez autenticados, mostrar segun rol dashboard o tienda --}}
  @auth

    @can('panel')
      <a wire:navigate href="{{ url('/dashboard') }}" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">
        {{__('Dashboard')}}
      </a>
    @endcan

    @can('tienda')
      <a wire:navigate href="#" class="text-white font-semibold border-b-2 line-clamp-2 capitalize">
        <span>tienda</span>
      </a>
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
