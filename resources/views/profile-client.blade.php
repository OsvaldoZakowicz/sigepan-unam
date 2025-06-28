@extends('welcome')

@section('view_content')
  <div class="pt-5 mt-20 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 pb-14">
    {{-- * vista de perfil de cliente, layout. --}}
    <div class="flex items-start justify-between w-full max-w-5xl gap-8 p-6 mx-auto bg-white rounded-lg">
      <div class="m-2 md:m-4 lg:m-8">
        <div class="flex flex-wrap items-start gap-8">

          <p class="text-lg font-semibold text-neutral-800">Mi Perfil:</p>

          <div class="w-full bg-white rounded-sm shadow">
            {{-- componente para mostrar cuadro del perfil del cliente --}}
            @livewire('users.show-profile-client')
          </div>

          <p class="font-semibold text-neutral-800">Configuraciones rápidas:</p>

          <div class="flex flex-wrap items-start w-full gap-8 lg:flex-nowrap">
            <div class="bg-white rounded-sm shadow sm:w-full grow md:w-1/2">
              <livewire:profile.update-profile-information-form />
            </div>

            <div class="bg-white rounded-sm shadow sm:w-full grow md:w-1/2">
              <livewire:profile.update-password-form />
            </div>

            <div class="bg-red-100 border border-red-400 rounded-sm shadow lg:w-1/3">
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
