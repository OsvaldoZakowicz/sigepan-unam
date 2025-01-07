<x-app-layout>
  {{-- encabezado de vista --}}
  <x-slot name="header">

    <div class="w-full flex gap-10 h-10 justify-start items-center text-sm font-medium capitalize text-neutral-700">

      {{-- titulo de vista --}}
      <span>@yield('view_title')</span>

      {{-- layout dinamico de blade para navegacion de vistas --}}
      <nav class="flex justify-start items-center h-10">
        @yield('view_nav')
      </nav>

    </div>

  </x-slot>

  {{-- seccion de vista --}}
  {{-- layout dinamico de blade para vistas con componentes livewire --}}
  <section class="w-full">
    @yield('view_content')
  </section>
</x-app-layout>
