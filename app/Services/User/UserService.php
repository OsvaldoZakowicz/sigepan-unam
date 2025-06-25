<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserService
{
  //* reglas sobre los roles
  protected $INTERNAL_ROLE_ATTRIBUTE = 'is_internal';
  protected $RESTRICTED_ROLE = 'proveedor';
  protected $EXTERNAL_ROLE = 'cliente';

  //* obtener atributo de rol interno
  public function getInternalRoleAttribute()
  {
    return $this->INTERNAL_ROLE_ATTRIBUTE;
  }

  //* obtener nombre del rol restringido
  public function getRestrictedRole()
  {
    return $this->RESTRICTED_ROLE;
  }

  //* obtener nombre del rol externo
  public function getExternalRole()
  {
    return $this->EXTERNAL_ROLE;
  }

  /**
   * * servicio: crear un usuario interno con rol
   * NOTA: el array debe recibir pares key => value validados
   * NOTA: el nombre de las 'key' depende de la implementacion de los nombres en inputs
   */
  public function createInternalUser(array $internal_user_data): User
  {
    $user = User::create([
      'name' => $internal_user_data['user_name'],
      'email' => $internal_user_data['user_email'],
      'password' => bcrypt($internal_user_data['user_password']),
    ]);

    $user->assignRole($internal_user_data['user_role']);

    return $user;
  }

  /**
   * * servicio: el usuario recibido es el mismo en session?
   * si el user->id recibido es el mismo a Auth::id() retorn true,
   * entonces el usuario recibido es el mismo en sesion
   */
  public function isUserOnSession(User $user): bool
  {
    return ($user->id === Auth::id()) ? true : false;
  }

  /**
   * * servicio: el usuario recibido tiene rol proveedor,
   * y tiene un proveedor asociado?
   */
  public function isSupplierUserWithSupplier(User $user): bool
  {
    $user_role = $user->getRolenames()->first();
    $user_supplier = $user->supplier;

    // es usuario con rol proveedor
    if ($user_role === $this->RESTRICTED_ROLE) {
      // tiene un provedor asociado
      if ($user_supplier !== null) {
        return true; // verdadero a todo
      }
      // sea o no proveedor
      return false;
    }
    // sea o no proveedor
    return false;
  }

  /**
   * * servicio: el usuario recibido es un cliente?
   */
  public function isClientUser(User $user): bool
  {
    $user_role = $user->getRolenames()->first();
    return ($user_role === $this->EXTERNAL_ROLE) ? true : false;
  }

  /**
   * borrar usuario interno
   * @param User $user
   * @return void
   */
  public function deleteInternalUser(User $user): void
  {
    DB::transaction(function () use ($user) {
      // Quitar roles y permisos
      $user->syncRoles([]);
      $user->syncPermissions([]);

      // Orden: Profile primero (tiene las FK), luego las referenciadas
      $user->profile?->delete();
      $user->profile?->address?->delete();
      $user->delete();
    });
  }

  /**
   * restaurar usuario interno
   * @param int $id
   * @return void
   */
  public function restoreInternalUser(int $id): void
  {
    $user = User::withTrashed()->findOrFail($id);

    DB::transaction(function () use ($user) {
      // Cargar profile con withTrashed para acceder a datos soft deleted
      $profile = $user->profile()->withTrashed()->first();

      // Orden: Primero las referenciadas, luego las que tienen FK
      if ($profile && $profile->address_id) {
        Address::withTrashed()->find($profile->address_id)?->restore();
      }

      $user->restore();
      $profile?->restore();
    });
  }

  /**
   * * servicio: editar usuario interno
   */
  public function editInternalUser(User $user, array $internal_user_data): User
  {
    $user->name = $internal_user_data['user_name'];
    $user->email = $internal_user_data['user_email'];
    $user->save();

    $user->syncRoles($internal_user_data['user_role']);

    return $user;
  }

  /**
   * * servicio: editar usuario interno y solo cambiar el rol
   */
  public function editRoleInternalUser(User $user, array $internal_user_data): User
  {
    $user->syncRoles($internal_user_data['user_role']);

    return $user;
  }
}
