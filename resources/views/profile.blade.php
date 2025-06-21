<x-app-layout>
  {{-- perfil de usuario --}}
  <x-slot name="header">
    <div class="flex items-center justify-start w-full h-10 gap-10 text-sm font-medium capitalize text-neutral-700">
      <span>mi perfil</span>
    </div>
  </x-slot>

  <div class="m-2 md:m-4 lg:m-8">
    <div class="flex flex-wrap items-start gap-8">

      <div class="w-full bg-white rounded-sm shadow">
        @livewire('users.show-profile')
      </div>

      <div class="flex flex-wrap items-start w-full gap-8 lg:flex-nowrap">
        <div class="bg-white rounded-sm shadow sm:w-full grow md:w-1/2">
          <livewire:profile.update-profile-information-form />
        </div>

        <div class="bg-white rounded-sm shadow sm:w-full grow md:w-1/2">
          <livewire:profile.update-password-form />
        </div>

        {{-- <div class="bg-red-200 border border-red-400 rounded-sm shadow lg:w-1/3">
          <livewire:profile.delete-user-form />
        </div> --}}
      </div>

    </div>
  </div>
</x-app-layout>
