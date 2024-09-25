<x-app-layout>
  {{-- encabezado de vista --}}
  <x-slot name="header">
    <div class="w-full flex gap-10 h-10 justify-start items-center text-sm font-medium capitalize text-neutral-700">
      {{-- titulo de vista --}}
      <span>
        @yield('view_title')
      </span>
      {{-- navegacion de la vista --}}
      <nav class="flex justify-start items-center h-10">
        @yield('view_nav')
      </nav>
    </div>
    {{-- layout dinamico de blade para navegacion de vistas --}}
  </x-slot>
  {{-- seccion de vista --}}
  <section class="w-full">
    {{-- layout dinamico de blade para vistas --}}
    @yield('view_content')
  </section>
</x-app-layout>
