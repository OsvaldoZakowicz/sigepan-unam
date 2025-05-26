{{-- navegacion del modulo de estadisticas --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  <x-nav-link
    wire:navigate
    :href="route('stats-stats-index')"
    :active="request()->routeIs('stats-stats-*')"
    >estadisticas
  </x-nav-link>

</div>
