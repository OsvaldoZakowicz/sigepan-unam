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
  {{-- notificaciones recibidas desde eventos livewire --}}
  <section
    x-data="{
      success: false,
      error: false,
      info: false,
      type: '',
      title: '',
      descr: '',
      catch_event(toast_data) {

        this.type = toast_data['event_type']
        this.title = toast_data['title_toast']
        this.descr = toast_data['descr_toast']

        if(this.type === 'success') {
          this.toggle_success()
        }

        if(this.type === 'error') {
          this.toggle_error()
        }

        if(this.type === 'info') {
          this.toggle_info()
        }

      },
      toggle_success() { this.success = ! this.success },
      toggle_error() { this.error = ! this.error },
      toggle_info() { this.info = ! this.info },
    }"
    class="relative w-full">

    {{-- contenedor de notificaciones --}}
    {{-- capturo el evento con nombre --}}
    <div x-on:toast-event.window="catch_event($event.detail.toast_data)">

      {{-- mensaje toast exito --}}
      <div x-cloak x-show="success" x-on:click.outside="toggle_success" class="absolute z-50 top-0 left-2 lg:inset-x-1/3">
        <div class="relative flex gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 max-w-lg">
          <div class="mt-0.5">
            <span class="text-xl text-emerald-500">&#10003;</span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-neutral-800" x-text="title"></span>
            <span class="text-sm text-neutral-600" x-text="descr"></span>
            <span x-on:click="toggle_success" class="absolute top-2 right-2 text-green-500 hover:text-green-700 cursor-pointer">&#10005;</span>
          </div>
        </div>
      </div>

      {{-- mensaje toast error --}}
      <div x-cloak x-show="error" x-on:click.outside="toggle_error" class="absolute z-50 top-0 left-2 lg:inset-x-1/3">
        <div class="relative flex gap-3 p-4 rounded-lg border bg-red-50 border-red-200 max-w-lg">
          <div class="mt-0.5">
            <span class="text-xl text-red-500">&#33;</span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-neutral-800" x-text="title"></span>
            <span class="text-sm text-neutral-600" x-text="descr"></span>
            <span x-on:click="toggle_error" class="absolute top-2 right-2 text-red-500 hover:text-red-700 cursor-pointer">&#10005;</span>
          </div>
        </div>
      </div>

      {{-- mensaje toast info --}}
      <div x-cloak x-show="info" x-on:click.outside="toggle_info" class="absolute z-50 top-0 left-2 lg:inset-x-1/3">
        <div class="relative flex gap-3 p-4 rounded-lg border bg-blue-50 border-blue-200 max-w-lg">
          <div class="mt-0.5">
            <span class="text-xl text-blue-500">&#33;</span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-neutral-800" x-text="title"></span>
            <span class="text-sm text-neutral-600" x-text="descr"></span>
            <span x-on:click="toggle_info" class="absolute top-2 right-2 text-blue-500 hover:text-blue-700 cursor-pointer">&#10005;</span>
          </div>
        </div>
      </div>

    </div>

    {{-- layout dinamico de blade para vistas con componentes livewire --}}
    @yield('view_content')
  </section>
</x-app-layout>
