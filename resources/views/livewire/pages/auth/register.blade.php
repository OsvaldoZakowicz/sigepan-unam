<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
      <!-- Name -->
      <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
      </div>

      <!-- Email Address -->
      <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
        <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
      </div>

      <!-- Password -->
      <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
        <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required autocomplete="new-password" />

      </div>

      <!-- Confirm Password -->
      <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password" />

      </div>

      <div class="flex flex-col gap-8 items-center justify-center">
        <div class="flex items-center justify-end gap-4 w-full mt-4">
          <a wire:navigate href="{{ route('welcome') }}" class="flex justify-center items-center box-border w-fit h-6 my-1 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">cancelar</a>

          <button type="submit" class="flex justify-center items-center box-border w-fit h-6 my-1 p-1 border-solid border rounded border-blue-600 bg-blue-600 text-center text-neutral-100 uppercase text-xs">
            {{ __('Register') }}
          </button>
        </div>
        <div>
          <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('login') }}" wire:navigate>
            <span class="capitalize">¿ya se registro anteriormente?: inicie sesión</span>
          </a>
        </div>
      </div>
  </form>
</div>
