<nav class="mx-3 flex flex-1 justify-end gap-4">
  {{-- * navegacion de la vista bienvenida --}}
  {{-- * una vez autenticados, mostrar segun rol dashboard o tienda --}}
  @auth

    @can('panel')
      <a wire:navigate href="{{ url('/dashboard') }}" class="capitalize">
        {{__('Dashboard')}}
      </a>
    @endcan

    @can('tienda')
      <a wire:navigate href="#" class="capitalize">
        <span>tienda</span>
      </a>
    @endcan

    @can('tienda-perfil')
      <a wire:navigate href="#" class="capitalize">
        <span>mi perfil</span>
      </a>
    @endcan

    {{-- logout manual del cliente --}}
    @can('tienda')
      <form action="{{ route('client-logout') }}">
        @csrf
        <button type="submit" class="capitalize">cerrar sesión</button>
      </form>
    @endcan

  @else

    {{-- * nadie autenticado, mostrar login y register --}}

    <a wire:navigate href="{{ route('login') }}" class="capitalize">
      {{__('Log in')}}
    </a>

    @if (Route::has('register'))
      <a wire:navigate href="{{ route('register') }}" class="capitalize">
        {{__('Register')}}
      </a>
    @endif

  @endauth
</nav>
