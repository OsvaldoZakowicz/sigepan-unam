<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
  /**
   * Mark the authenticated user's email address as verified.
   */
  public function __invoke(EmailVerificationRequest $request): RedirectResponse
  {
    if ($request->user()->hasVerifiedEmail()) {

      // clientes van a la tienda
      if ($request->user()->hasRole('cliente')) {
        return redirect()->intended(route('store-store-index', absolute: false) . '?verified=1');
      }

      // otros roles van a dashboard
      return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }

    if ($request->user()->markEmailAsVerified()) {
      event(new Verified($request->user()));
    }

    // clientes van a la tienda
    if ($request->user()->hasRole('cliente')) {
      return redirect()->intended(route('store-store-index', absolute: false) . '?verified=1');
    }

    // otros roles van a dashboard
    return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
  }
}
