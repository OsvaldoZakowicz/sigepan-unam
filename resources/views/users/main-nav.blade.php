{{-- navegacion principal del modulo --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">
  <x-nav-link wire:navigate :href="route('users-users-index')" :active="request()->routeIs('users-users-*')">usuarios</x-nav-link>
  <x-nav-link wire:navigate :href="route('users-roles-index')" :active="request()->routeIs('users-roles-*')">roles</x-nav-link>
  <x-nav-link wire:navigate :href="route('users-permissions-index')" :active="request()->routeIs('users-permissions-*')">permisos</x-nav-link>
</div>
