<x-app-layout>
  {{-- perfil de usuario --}}
  <x-slot name="header">
    <div class="w-full flex gap-10 h-10 justify-start items-center text-sm font-medium capitalize text-neutral-700">
      <span>mi perfil</span>
    </div>
  </x-slot>

  <div class="m-2 md:m-4 lg:m-8">
    <div class="flex items-start gap-8 flex-wrap">

      <div class="w-full bg-white shadow rounded-sm">
        @livewire('users.show-profile')
      </div>

      <div class="flex flex-wrap items-stretch lg:flex-nowrap gap-8 w-full">
        <div class="sm:w-full grow md:w-1/2 bg-white shadow rounded-sm">
          <livewire:profile.update-profile-information-form />
        </div>

        <div class="sm:w-full grow md:w-1/2 bg-white shadow rounded-sm">
          <livewire:profile.update-password-form />
        </div>
      </div>

      <div class="w-full bg-red-200 border border-red-400 shadow rounded-sm">
        <livewire:profile.delete-user-form />
      </div>
    </div>
  </div>
</x-app-layout>
