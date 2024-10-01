<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
  <div class="mb-4 text-sm text-neutral-600">
    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
  </div>

  @if (session('status') == 'verification-link-sent')
    <div class="mb-4 font-medium text-sm text-green-600">
      {{ __('A new verification link has been sent to the email address you provided during registration.') }}
    </div>
  @endif

  <div class="mt-4 flex items-center justify-between">
    <button wire:click="sendVerification" type="submit" class="flex justify-center items-center box-border w-fit h-6 m-1 p-1 border-solid border rounded border-blue-600 bg-blue-600 text-center text-neutral-100 uppercase text-xs">
      {{ __('Resend Verification Email') }}
    </button>

    <button wire:click="logout" type="submit"
      class="underline text-sm text-neutral-600 hover:text-neutral-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
      {{ __('Log Out') }}
    </button>
  </div>
</div>
