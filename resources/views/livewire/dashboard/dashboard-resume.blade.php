<div class="w-full">
  {{-- card container --}}
  <main class="w-full flex gap-4 items-start justify-start flex-wrap my-2 mx-8">

    {{-- cards de informacion, visibles segun rol --}}

    {{-- datos del negocio --}}
    @livewire('dashboard.show-negocio')

    {{-- datos de tienda --}}
    @livewire('dashboard.show-tienda')


  </main>
</div>
