{{-- navegacion principal del modulo --}}
<div class="flex justify-start items-center gap-4 font-normal text-md">
  <x-nav-link wire:navigate :href="route('audits-audits-index')" :active="request()->routeIs('audits-audits-*')">auditoria</x-nav-link>
</div>
