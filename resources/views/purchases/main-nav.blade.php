{{-- navegacion del modulo de compras --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  <x-nav-link
    wire:navigate
    :href="route('purchases-purchases-index')"
    :active="request()->routeIs('purchases-purchases-*')"
    >compras
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('purchases-preorders-index')"
    :active="request()->routeIs('purchases-preorders-*')"
    >pre ordenes
  </x-nav-link>

</div>
