<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
  public string $password = '';

  /**
   * Delete the currently authenticated user.
   */
  public function deleteUser(Logout $logout): void
  {
    $this->validate([
        'password' => ['required', 'string', 'current_password'],
    ]);

    $user = Auth::user();
    $user_address = null;

    if ($user->profile) {
      $user_address = $user->profile->address;
    }

    // borrar usuario
    tap($user, $logout(...))->delete();

    if ($user_address) {
      $user_address->delete();
    }

    $this->redirect('/', navigate: true);
  }
}; ?>

<section class="m-2">
  <header class="m-2">
    <h2 class="text-lg font-medium text-neutral-900">
      <span class="capitalize">{{ __('Delete Account') }}</span>
    </h2>

    <p class="mt-1 text-sm text-neutral-600">
      {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>
  </header>

  <div class="w-full flex items-center justify-end gap-8 py-2 px-1">
    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" type="submit" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-red-600 bg-red-600 text-center text-neutral-100 uppercase text-xs">borrar cuenta</button>
  </div>

  <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
    <form wire:submit="deleteUser" class="p-6">

      <h2 class="text-lg font-medium text-neutral-900">
        {{ __('Are you sure you want to delete your account?') }}
      </h2>

      <p class="mt-1 text-sm text-neutral-600">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
      </p>

      <div class="mt-6">
        <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

        <x-text-input
          wire:model="password"
          id="password"
          name="password"
          type="password"
          class="mt-1 block w-3/4"
          placeholder="{{ __('Password') }}"
        />

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div class="mt-6 flex justify-end gap-4">

        <button x-on:click="$dispatch('close')" type="button" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-600 bg-neutral-600 text-center text-neutral-100 uppercase text-xs">cancelar</button>

        <button type="submit" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-red-600 bg-red-600 text-center text-neutral-100 uppercase text-xs">borrar definitivamente</button>
      </div>
    </form>
  </x-modal>
</section>
