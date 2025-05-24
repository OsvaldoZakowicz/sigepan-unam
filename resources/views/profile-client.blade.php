@extends('welcome')

@section('view_content')
  <div class="mt-20 pt-5 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 pb-14">
    {{-- * vista de perfil de cliente, layout. --}}
    <div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-7xl mx-auto p-6">
      <div class="m-2 md:m-4 lg:m-8">
        <div class="flex items-start gap-8 flex-wrap">

          <p class="text-lg font-semibold text-neutral-800">Mi Perfil:</p>

          <div class="w-full bg-white shadow rounded-sm">
            {{-- componente para mostrar cuadro del perfil del cliente --}}
            @livewire('users.show-profile-client')
          </div>

          <p class="font-semibold text-neutral-800">Configuraciones rápidas:</p>

          <div class="flex flex-wrap items-start lg:flex-nowrap gap-8 w-full">
            <div class="sm:w-full grow md:w-1/2 bg-white shadow rounded-sm">
              <livewire:profile.update-profile-information-form />
            </div>

            <div class="sm:w-full grow md:w-1/2 bg-white shadow rounded-sm">
              <livewire:profile.update-password-form />
            </div>

            <div class="lg:w-1/3 bg-red-200 border border-red-400 shadow rounded-sm">
              <livewire:profile.delete-user-form />
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

@section('view_footer')
  @livewire('store.footer-section')
@endsection
