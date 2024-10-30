{{-- navegacion del modulo de stock --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">
  <x-nav-link wire:navigate :href="route('stocks-stocks-index')" :active="request()->routeIs('stocks-stocks-*')">mis productos</x-nav-link>
  <x-nav-link wire:navigate :href="route('stocks-measures-index')" :active="request()->routeIs('stocks-measures-*')">unidades de medida</x-nav-link>
</div>
