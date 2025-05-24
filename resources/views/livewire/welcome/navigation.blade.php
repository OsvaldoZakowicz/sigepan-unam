<nav class="bg-neutral-800 py-6 px-6 flex justify-between items-center">

  {{-- * navegacion de la vista bienvenida Y TIENDA --}}

  {{-- titulo de la panaderia --}}
  @livewire('store.navigation-section')

  {{-- * una vez autenticados, mostrar segun rol dashboard o tienda --}}
  <div class="flex items-center gap-8">
    @auth
      <span class="text-orange-100 capitalize italic font-light">
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
          :active="request()->routeIs('store-store-index') || request()->routeIs('store-store-cart-index')"
          wire:navigate
          >tienda
        </x-nav-link-store>
      @endcan
      @can('tienda')
        <x-nav-link-store
          :href="route('store-store-orders-list')"
          :active="request()->routeIs('store-store-orders-*')"
          wire:navigate
          >mis pedidos
        </x-nav-link-store>
      @endcan
      @can('tienda')
        <x-nav-link-store
          :href="route('store-store-purchases-list')"
          :active="request()->routeIs('store-store-purchases-*')"
          wire:navigate
          >mis compras
        </x-nav-link-store>
      @endcan
      {{-- dropdown del menu desktop --}}
      <div class="hidden sm:flex sm:items-center sm:ms-6">
        <x-dropdown align="right" width="48">
          {{-- boton abrir y cerrar dropdown --}}
          <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-neutral-700 bg-orange-100 transition ease-in-out duration-150">
              {{-- nombre --}}
              <div
                x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                x-text="name"
                x-on:profile-updated.window="name = $event.detail.name"
                class="capitalize">
              </div>
              {{-- svg --}}
              <div class="ms-1">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </button>
          </x-slot>
          {{-- contenido del dropdown --}}
          <x-slot name="content" class="bg-orange-100">
            {{-- ir al perfil --}}
            @can('tienda-perfil')
              <x-dropdown-link
                :href="route('profile')"
                wire:navigate
                >{{ __('Profile') }}
              </x-dropdown-link>
            @endcan

            {{-- cerrar sesion --}}
            @can('tienda')
              <x-dropdown-link>
                <form action="{{ route('client-logout') }}">
                  @csrf
                  <button type="submit">cerrar sesión</button>
                </form>
              </x-dropdown-link>
            @endcan
          </x-slot>
        </x-dropdown>
      </div>
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
  </div>

</nav>
