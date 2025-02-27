{{-- navegacion principal de la seccion --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  <x-nav-link
    wire:navigate
    :href="route('quotations-quotations-index')"
    :active="request()->routeIs('quotations-quotations-*')"
    >presupuestos recibidos
  </x-nav-link>

  <x-nav-link
    wire:navigate
    :href="route('quotations-preorders-index')"
    :active="request()->routeIs('quotations-preorders-*')"
    >pre ordenes recibidas
  </x-nav-link>

</div>
