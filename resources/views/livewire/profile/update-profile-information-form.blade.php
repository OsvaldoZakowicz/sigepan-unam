<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
  <header>
      <h2 class="text-lg font-medium text-neutral-900">
          {{-- {{ __('Profile Information') }} --}}
          <span class="capitalize">credenciales de acceso</span>
      </h2>

      <p class="mt-1 text-sm text-neutral-600">
        <span>Actualiza tu nombre de usuario y email, si cambias el email, deber&aacute;s volver a verificarlo.</span>
      </p>
  </header>

  <form wire:submit="updateProfileInformation" class="mt-6 space-y-4">
    {{-- nombre de usuario --}}
    <div>
      <x-input-label for="name" :value="'Nombre de usuario'" />
      <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
      <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
      <x-input-label for="email" :value="__('Email')" />
      <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
      <x-input-error class="mt-2" :messages="$errors->get('email')" />

      @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
          <div>
              <p class="text-sm mt-2 text-gray-800">
                  {{ __('Your email address is unverified.') }}

                  <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      {{ __('Click here to re-send the verification email.') }}
                  </button>
              </p>

              @if (session('status') === 'verification-link-sent')
                  <p class="mt-2 font-medium text-sm text-green-600">
                      {{ __('A new verification link has been sent to your email address.') }}
                  </p>
              @endif
          </div>
      @endif
    </div>

    <div class="flex items-center gap-4">
      {{-- <x-primary-button>{{ __('Save') }}</x-primary-button> --}}
      <button type="submit" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-emerald-600 bg-emerald-600 text-center text-neutral-100 uppercase text-xs">guardar</button>

      <x-action-message class="me-3" on="profile-updated">
        <span class="capitalize">perfil actualizado</span>
      </x-action-message>
    </div>
  </form>
</section>
