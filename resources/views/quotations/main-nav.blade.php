{{-- navegacion principal de la seccion --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">

  <x-nav-link
    wire:navigate
    :href="route('quotations-quotations-index')"
    :active="request()->routeIs('quotations-quotations-*')"
    >mis presupuestos
  </x-nav-link>

</div>
