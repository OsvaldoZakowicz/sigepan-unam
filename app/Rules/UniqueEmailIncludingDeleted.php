<?php

namespace App\Rules;

use Closure;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEmailIncludingDeleted implements ValidationRule
{
  protected $ignoreUserId;

  public function __construct($ignoreUserId = null)
  {
    $this->ignoreUserId = $ignoreUserId;
  }

  /**
   * Run the validation rule.
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    // buscar en todos los usuarios, incluyendo soft-deleted
    $query = User::withTrashed()->where('email', $value);

    // si estamos actualizando, ignorar el usuario actual
    if ($this->ignoreUserId) {
      $query->where('id', '!=', $this->ignoreUserId);
    }

    $existingUser = $query->first();

    if ($existingUser) {
      if ($existingUser->trashed()) {
        $fail('Este email pertenece a una cuenta eliminada que no puede ser reutilizada.');
      } else {
        $fail('Este email ya está en uso.');
      }
    }
  }
}
