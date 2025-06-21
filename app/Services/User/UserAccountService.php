<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAccountService
{
  /**
   * Eliminar cuenta de usuario
   * - Usuario: soft delete
   * - Perfil: eliminación completa
   */
  public function deleteUserAccount(User $user): bool
  {
    return DB::transaction(function () use ($user) {
      try {
        // 1. eliminar completamente el perfil (hard delete)
        if ($user->profile) {
          $address = $user->profile->address;
          $user->profile->delete();
          $address->delete();
        }

        // 2. Soft delete del usuario
        $user->delete();

        Log::info("Usuario eliminado exitosamente", ['user_id' => $user->id]);

        return true;
      } catch (\Exception $e) {
        Log::error("Error al eliminar usuario", [
          'user_id' => $user->id,
          'error' => $e->getMessage()
        ]);
        throw $e;
      }
    });
  }

  /**
   * Recuperar cuenta eliminada
   */
  public function restoreUserAccount(User $user): bool
  {
    if (!$user->trashed()) {
      return false; // usuario no esta eliminado
    }

    return DB::transaction(function () use ($user) {
      try {
        // restaurar el usuario
        $user->restore();

        Log::info("Usuario restaurado exitosamente", ['user_id' => $user->id]);

        return true;
      } catch (\Exception $e) {
        Log::error("Error al restaurar usuario", [
          'user_id' => $user->id,
          'error' => $e->getMessage()
        ]);
        throw $e;
      }
    });
  }

  /**
   * Verificar si un usuario puede ser restaurado
   */
  public function canRestoreUser(string $email): ?User
  {
    return User::withTrashed()
      ->where('email', $email)
      ->whereNotNull('deleted_at')
      ->first();
  }
}
