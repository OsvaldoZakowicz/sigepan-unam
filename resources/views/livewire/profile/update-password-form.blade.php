<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="m-2">
  <header class="m-2">
      <h2 class="text-lg font-medium text-neutral-900">
          <span class="capitalize">{{ __('Update Password') }}</span>
      </h2>

      <p class="mt-1 text-sm text-neutral-600">
          {{ __('Ensure your account is using a long, random password to stay secure.') }}
      </p>
  </header>

  <form wire:submit="updatePassword" class="pt-2 px-1">
    <div class="flex flex-col gap-1 w-full py-2 px-1">
      <span>
        <x-input-label for="update_password_current_password" class="capitalize max-w-fit" :value="'contraseña actual'" />
        <span class="text-red-600">*</span>
        @error('current_password')
          <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
        @enderror
      </span>
      <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
    </div>

    <div class="flex flex-col gap-1 w-full py-2 px-1">
      <span>
        <x-input-label for="update_password_password" class="capitalize max-w-fit" :value="'nueva contraseña'" />
        <span class="text-red-600">*</span>
        @error('password')
          <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
        @enderror
      </span>
      <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>

    <div class="flex flex-col gap-1 w-full py-2 px-1">
      <span>
        <x-input-label for="update_password_password_confirmation" class="capitalize max-w-fit" :value="'confirmar nueva contraseña'" />
        <span class="text-red-600">*</span>
        @error('password_confirmation')
          <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
        @enderror
      </span>
      <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>

    <div class="flex items-center justify-end gap-4 mt-2">
      <x-action-message class="me-3" on="password-updated">
        <span class="capitalize">contraseña actualizada</span>
      </x-action-message>
      <button type="submit" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-emerald-600 bg-emerald-600 text-center text-neutral-100 uppercase text-xs">guardar</button>
    </div>
  </form>
</section>
