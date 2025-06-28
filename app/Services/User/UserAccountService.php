<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Profile;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAccountService
{
  /**
   * Eliminar cuenta de usuario
   * - Usuario: soft delete
   * - Perfil: soft delete
   */
  public function deleteUserAccount(User $user): bool
  {
    return DB::transaction(function () use ($user) {
      try {
        // 1. eliminar perfil
        if ($user->profile) {
          $address = $user->profile->address;
          $user->profile->delete();
          $address->delete();
        }

        // 2. eliminar usuario
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

        // cargar profile con withTrashed para acceder a datos soft deleted
        $profile = $user->profile()->withTrashed()->first();

        // orden: Primero las referenciadas, luego las que tienen FK
        if ($profile && $profile->address_id) {
          Address::withTrashed()->find($profile->address_id)?->restore();
        }

        $user->restore();
        $profile?->restore();

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

  /**
   * verificar si el usuario a restaurar tiene rol cliente
   */
  public function isClient(int $id): bool
  {
    $user = User::withTrashed()->find($id);

    return $user->roles()
      ->where('name', 'cliente')
      ->exists();
  }

  /**
   * verificar si el usuario en sesion no tiene
   * ordenes pendientes de pago, o de entrega.
   * @param User $user
   * @return bool
   */
  public function canDeleteAccount(User $user): bool
  {
    // id de estado de entrega de orden "pendiente"
    $order_status_pendiente_id = OrderStatus::ORDER_STATUS_PENDIENTE();

    // estado de pago "pendiente"
    $order_payment_status_pendiente = Order::ORDER_PAYMENT_STATUS_PENDIENTE();

    $count_status_pending = $user->orders()
      ->where('order_status_id', $order_status_pendiente_id)
      ->count();

    $count_payment_pending = $user->orders()
      ->where('payment_status', $order_payment_status_pendiente)
      ->count();

    return ($count_status_pending > 0 || $count_payment_pending > 0) ? true : false;
  }
}
