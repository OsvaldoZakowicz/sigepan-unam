{{-- navegacion principal del modulo --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">
  <x-nav-link wire:navigate :href="route('suppliers-suppliers-index')" :active="request()->routeIs('suppliers-suppliers-*')">proveedores</x-nav-link>
</div>
