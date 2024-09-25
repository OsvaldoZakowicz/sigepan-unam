<x-app-layout>
  {{-- titulo de vista --}}
  <x-slot name="header">
    <span>titulo de vista</span>
  </x-slot>
  {{-- seccion de vista --}}
  <section class="w-full">
    {{-- componentes --}}
    @livewire('roles.create-role')
  </section>
</x-app-layout>
