{{-- navegacion del modulo de ventas --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  <x-nav-link
    wire:navigate
    :href="route('sales-sales-index')"
    :active="request()->routeIs('sales-sales-*')"
    >ventas
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('sales-orders-index')"
    :active="request()->routeIs('sales-orders-*')"
    >pedidos
  </x-nav-link>

</div>
