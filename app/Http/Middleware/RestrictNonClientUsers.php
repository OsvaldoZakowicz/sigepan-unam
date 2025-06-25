<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictNonClientUsers
{
  /**
   * Handle an incoming request.
   * Si hay usuario autenticado y NO tiene rol cliente, denegar acceso
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (Auth::check()) {

      $user = User::find(Auth::id());

      if (!$user) {
        Auth::logout();
        return redirect()->route('login');
      }

      $has_client_role = $user->roles()
        ->where('name', 'cliente')
        ->exists();

      if (!$has_client_role) {
        abort(403, 'Acceso denegado. Esta sección es exclusiva para clientes.');
      }
    }

    return $next($request);
  }
}
