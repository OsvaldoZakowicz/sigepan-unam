<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
  /**
   * Log the current user out of the application.
   */
  public function logout(Logout $logout): void
  {
    $logout();

    $this->redirect('login', navigate: true);
  }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

  {{-- menu desktop --}}
  <div class="w-full px-8 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        <!-- Logo -->
        <div class="flex items-center shrink-0">
          <a href="{{ route('dashboard') }}" wire:navigate>
            <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex items-center justify-start gap-8 ml-10">

          {{-- panel --}}
          @can('panel')
          <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>{{
            __('Dashboard') }}
          </x-nav-link>
          @endcan

          {{-- todo: permiso de ruta --}}
          @can('estadisticas')
          <x-nav-link :href="route('stats-stats-index')" :active="request()->routeIs('stats-*')" wire:navigate>
            Estadisticas
          </x-nav-link>
          @endcan

          {{-- stock --}}
          @can('stock')
          <x-nav-link :href="route('stocks-products-index')" :active="request()->routeIs('stocks-*')" wire:navigate>
            Stock
          </x-nav-link>
          @endcan

          @can('ventas')
          <x-nav-link :href="route('sales-sales-index')" :active="request()->routeIs('sales-*')" wire:navigate>Ventas
          </x-nav-link>
          @endcan

          @can('compras')
          <x-nav-link :href="route('purchases-purchases-index')" :active="request()->routeIs('purchases-*')"
            wire:navigate>Compras
          </x-nav-link>
          @endcan

          {{-- proveedores --}}
          @can('proveedores')
          <x-nav-link :href="route('suppliers-suppliers-index')" :active="request()->routeIs('suppliers-*')"
            wire:navigate>Proveedores
          </x-nav-link>
          @endcan

          {{-- presupuestos --}}
          @can('presupuestos')
          <x-nav-link :href="route('quotations-quotations-index')" :active="request()->routeIs('quotations-*')"
            wire:navigate>Presupuestos
          </x-nav-link>
          @endcan

          {{-- usuarios --}}
          @can('usuarios')
          <x-nav-link :href="route('users-users-index')" :active="request()->routeIs('users-*')" wire:navigate>Usuarios
          </x-nav-link>
          @endcan

          {{-- auditoria --}}
          @can('auditoria')
          <x-nav-link :href="route('audits-audits-index')" :active="request()->routeIs('audits-*')" wire:navigate>
            Auditoria
          </x-nav-link>
          @endcan

        </div>
      </div>

      {{-- dropdown del menu desktop --}}
      <div class="hidden sm:flex sm:items-center sm:ms-6">
        <x-dropdown align="right" width="48">
          {{-- boton abrir y cerrar dropdown --}}
          <x-slot name="trigger">
            <button
              class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
              {{-- nombre --}}
              <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                x-on:profile-updated.window="name = $event.detail.name" class="capitalize"></div>
              {{-- rol --}}
              <div>
                <span class="lowercase m-1 px-0.5 bg-neutral-100 border border-neutral-200 rounded-sm">
                  {{ auth()->user()->getRoleNames()->first() }}
                </span>
              </div>
              {{-- svg --}}
              <div class="ms-1">
                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </div>
            </button>
          </x-slot>
          {{-- contenido del dropdown --}}
          <x-slot name="content">
            {{-- ir al perfil --}}
            @can('panel-perfil')
            <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Profile') }}
            </x-dropdown-link>
            @endcan

            {{-- cerrar sesion --}}
            @can('panel-perfil')
            <button wire:click="logout" class="w-full text-start">
              <x-dropdown-link>{{ __('Log Out') }}</x-dropdown-link>
            </button>
            @endcan
          </x-slot>
        </x-dropdown>
      </div>

      {{-- icono menu responsive --}}
      <div class="flex items-center -me-2 sm:hidden">
        <button @click="open = ! open"
          class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
          <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
              stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- menu mobile --}}
  <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">

      {{-- panel --}}
      @can('panel')
      <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>{{
        __('Dashboard') }}
      </x-responsive-nav-link>
      @endcan

      @can('estadisticas')
      <x-responsive-nav-link :href="route('stats-stats-index')" :active="request()->routeIs('dashboard')" wire:navigate>
        Estadisticas
      </x-responsive-nav-link>
      @endcan

      {{-- stock --}}
      @can('stock')
      <x-responsive-nav-link :href="route('stocks-products-index')" :active="request()->routeIs('stocks-*')"
        wire:navigate>
        Stock
      </x-responsive-nav-link>
      @endcan

      @can('ventas')
      <x-responsive-nav-link :href="route('sales-sales-index')" :active="request()->routeIs('sales-*')" wire:navigate>
        Ventas
      </x-responsive-nav-link>
      @endcan

      @can('compras')
      <x-responsive-nav-link :href="route('purchases-purchases-index')" :active="request()->routeIs('purchases-*')"
        wire:navigate>
        Compras
      </x-responsive-nav-link>
      @endcan

      {{-- proveedores --}}
      @can('proveedores')
      <x-responsive-nav-link :href="route('suppliers-suppliers-index')" :active="request()->routeIs('suppliers-*')"
        wire:navigate>
        Proveedores
      </x-responsive-nav-link>
      @endcan

      {{-- presupuestos --}}
      @can('presupuestos')
      <x-responsive-nav-link :href="route('quotations-quotations-index')" :active="request()->routeIs('quotations-*')"
        wire:navigate>Presupuestos
      </x-responsive-nav-link>
      @endcan

      {{-- usuarios --}}
      @can('usuarios')
      <x-responsive-nav-link :href="route('users-users-index')" :active="request()->routeIs('users-*')" wire:navigate>
        Usuarios
      </x-responsive-nav-link>
      @endcan

      {{-- auditoria --}}
      @can('auditoria')
      <x-responsive-nav-link :href="route('audits-audits-index')" :active="request()->routeIs('audits-*')"
        wire:navigate>Auditoria
      </x-responsive-nav-link>
      @endcan

    </div>

    {{-- dropdown del menu mobile --}}
    <div class="pt-4 pb-1 border-t border-gray-200">
      <div class="px-4">
        <div class="text-base font-medium text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
          x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
        <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
      </div>

      <div class="mt-3 space-y-1">
        {{-- ir al perfil --}}
        <x-responsive-nav-link :href="route('profile')" wire:navigate>{{ __('Profile') }}
        </x-responsive-nav-link>

        {{-- cerrar sesion --}}
        <button wire:click="logout" class="w-full text-start">
          <x-responsive-nav-link>{{ __('Log Out') }}</x-responsive-nav-link>
        </button>
      </div>
    </div>
  </div>
</nav>