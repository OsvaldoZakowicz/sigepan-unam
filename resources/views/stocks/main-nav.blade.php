{{-- navegacion del modulo de stock --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  {{-- * temporalmente oculto --}}
  {{-- <x-nav-link
    wire:navigate
    :href="route('stocks-stocks-index')"
    :active="request()->routeIs('stocks-stocks-*')"
    >stock
  </x-nav-link> --}}

  <x-nav-link
    wire:navigate
    :href="route('stocks-products-index')"
    :active="request()->routeIs('stocks-products-*')"
    >productos
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('stocks-recipes-index')"
    :active="request()->routeIs('stocks-recipes-*')"
    >recetas
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('stocks-tags-index')"
    :active="request()->routeIs('stocks-tags-*')"
    >etiquetas
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('stocks-measures-index')"
    :active="request()->routeIs('stocks-measures-*')"
    >unidades de medida
  </x-nav-link>

</div>
