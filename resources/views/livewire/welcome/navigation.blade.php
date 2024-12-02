<nav class="mx-3 flex flex-1 justify-end gap-4">
  {{-- navegacion de la vista bienvenida --}}
  @auth

    {{-- una vez autenticados, mostrar segun rol dashboard o tienda --}}

    @can('panel')
      <a wire:navigate href="{{ url('/dashboard') }}" class="capitalize">
        {{__('Dashboard')}}
      </a>
    @endcan

    @can('cliente')
      <a wire:navigate href="#" class="capitalize">
        <span>tienda</span>
      </a>
      <a wire:navigate href="#" class="capitalize">
        <span>mi perfil</span>
      </a>
      {{-- logout manual del cliente --}}
      <form action="{{ route('client-logout') }}">
        @csrf
        <button type="submit" class="capitalize">cerrar sesión</button>
      </form>
    @endcan

  @else

    {{-- nadie autenticado, mostrar login y register --}}

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
