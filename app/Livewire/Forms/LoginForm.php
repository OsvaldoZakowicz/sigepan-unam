<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Lockout;
use App\Services\User\UserAccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginForm extends Form
{
  #[Validate('required|string|email')]
  public string $email = '';

  #[Validate('required|string')]
  public string $password = '';

  #[Validate('boolean')]
  public bool $remember = false;

  protected ?UserAccountService $userAccountService = null;

  /**
   * Attempt to authenticate the request's credentials.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function authenticate(): array
  {
    $this->userAccountService ??= app(UserAccountService::class);

    $this->ensureIsNotRateLimited();

    // Primero verificar si existe un usuario eliminado con este email
    $deletedUser = $this->userAccountService->canRestoreUser($this->email);

    if ($deletedUser && Hash::check($this->password, $deletedUser->password)) {
      // Usuario eliminado con credenciales correctas
      return [
        'type' => 'deleted_user',
        'user' => $deletedUser,
        'message' => 'Tu cuenta fue eliminada. ¿Deseas recuperarla?'
      ];
    }

    if (!Auth::attempt($this->only(['email', 'password']), $this->remember)) {
      RateLimiter::hit($this->throttleKey());

      throw ValidationException::withMessages([
        'form.email' => trans('auth.failed'),
      ]);
    }

    RateLimiter::clear($this->throttleKey());

    return [
      'type' => 'success',
      'user' => Auth::user()
    ];
  }

  /**
   * Restaurar cuenta eliminada y autenticar
   */
  public function restoreAndAuthenticate(User $deletedUser): bool
  {
    $this->userAccountService ??= app(UserAccountService::class);

    if ($this->userAccountService->restoreUserAccount($deletedUser)) {
      // Autenticar al usuario restaurado
      Auth::login($deletedUser, $this->remember);
      return true;
    }

    return false;
  }

  /**
   * Ensure the authentication request is not rate limited.
   */
  protected function ensureIsNotRateLimited(): void
  {
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
      return;
    }

    event(new Lockout(request()));

    $seconds = RateLimiter::availableIn($this->throttleKey());

    throw ValidationException::withMessages([
      'form.email' => trans('auth.throttle', [
        'seconds' => $seconds,
        'minutes' => ceil($seconds / 60),
      ]),
    ]);
  }

  /**
   * Get the authentication rate limiting throttle key.
   */
  protected function throttleKey(): string
  {
    return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
  }
}
