{{-- navegacion principal del modulo --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  <x-nav-link
    wire:navigate
    :href="route('suppliers-suppliers-index')"
    :active="request()->routeIs('suppliers-suppliers-*')"
    >proveedores
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('suppliers-budgets-periods-index')"
    :active="request()->routeIs('suppliers-budgets-*')"
    >periodo presupuestario
  </x-nav-linkwire>

  <x-nav-link
    wire:navigate
    :href="route('suppliers-provisions-index')"
    :active="request()->routeIs('suppliers-provisions-*')"
    >suministros
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('suppliers-trademarks-index')"
    :active="request()->routeIs('suppliers-trademarks-*')"
    >marcas
  </x-nav-linkwire>

</div>
