<x-app-layout>
  {{-- perfil de usuario --}}
  <x-slot name="header">
    <div class="w-full flex gap-10 h-10 justify-start items-center text-sm font-medium capitalize text-neutral-700">
      <span>mi perfil</span>
    </div>
  </x-slot>

  <div class="m-2">
    {{-- todo: layout --}}
    <div class="space-y-4">
      <div class="overflow-hidden p-4 sm:p-8 bg-white shadow rounded-sm">
        {{-- todo: tarjeta con informacion del usuario --}}
      </div>

      <div class="overflow-hidden p-4 sm:p-8 bg-white shadow rounded-sm">
        @livewire('users.complete-profile')
      </div>

      <div class="p-4 sm:p-8 bg-white shadow rounded-sm">
        <livewire:profile.update-profile-information-form />
      </div>

      <div class="p-4 sm:p-8 bg-white shadow rounded-sm">
        <livewire:profile.update-password-form />
      </div>

      <div class="p-4 sm:p-8 bg-white shadow rounded-sm">
        <livewire:profile.delete-user-form />
      </div>
    </div>
  </div>
</x-app-layout>
