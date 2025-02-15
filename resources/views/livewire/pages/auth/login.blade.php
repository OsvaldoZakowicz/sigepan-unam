<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
  public LoginForm $form;

  /**
   * Handle an incoming authentication request.
   */
  public function login(): void
  {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    if(auth()->user()->hasRole('cliente')) {

      /* redirigir a vista de bienvenida, pero ya autenticado */
      $this->redirectIntended(default: route('welcome', absolute: false), navigate: true);

    } else {

      /* redirigir a vista de panel, pero ya autenticado */
      $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

    }

  }
}; ?>

<div>
  <!-- Session Status -->
  <x-auth-session-status class="mb-4" :status="session('status')" />
  <form wire:submit="login" class="rounded-sm">
    <!-- Email Address -->
    <div>
      <x-input-label for="email" :value="__('Email')" />
      <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
      <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
    </div>

    <!-- Password -->
    <div class="mt-4">
      <x-input-label for="password" :value="__('Password')" />
      <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
      <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                      type="password"
                      name="password"
                      required autocomplete="current-password" />

    </div>

    <!-- Remember Me -->
    <div class="block mt-4">
      <label for="remember" class="inline-flex items-center">
        <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-neutral-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
        <span class="ms-2 text-sm text-neutral-600">{{ __('Remember me') }}</span>
      </label>
    </div>

    <div class="flex items-center gap-4 justify-end mt-4">
      @if (Route::has('password.request'))
        <a class="underline text-sm text-neutral-600 hover:text-neutral-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}" wire:navigate>
          {{ __('Forgot your password?') }}
        </a>
      @endif

      <a wire:navigate href="{{ route('welcome') }}" class="flex justify-center items-center box-border w-fit h-6 my-1 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">cancelar</a>

      <button type="submit" class="flex justify-center items-center box-border w-fit h-6 my-1 p-1 border-solid border rounded border-blue-600 bg-blue-600 text-center text-neutral-100 uppercase text-xs">
        {{ __('Log in') }}
      </button>
    </div>
  </form>
</div>
