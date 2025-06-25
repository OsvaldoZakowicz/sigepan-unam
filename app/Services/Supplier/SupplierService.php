<?php

namespace App\Services\Supplier;

use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\ProvisionType;
use InvalidArgumentException;
use App\Models\ProvisionTrademark;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupplierService
{
  // rol proveedor
  protected $SUPPLIER_ROLE = 'proveedor';

  /**
   * servicio: obtener condiciones frente al iva
   * @return Collection $iva_conditions
   */
  public function getSuppilerIvaConditions(): Collection
  {
    return DB::table('iva_conditions')->get();
  }

  /**
   * servicio: obtener rol proveedor
   * @return string $SUPPLIER_ROLE
   */
  public function getSupplierRolename(): string
  {
    return $this->SUPPLIER_ROLE;
  }

  /**
   * servicio: crear proveedor
   * NOTA: recibe un usuario creado previamente
   * NOTA: recibe el array de inputs validados
   * @param User $supplier_user
   * @param Array $supplier_data
   * @return Supplier $supplier
   */
  public function createSupplier(User $supplier_user, array $supplier_data): Supplier
  {
    $supplier_address = Address::create([
      'street'      => $supplier_data['company_street'],
      'number'      => $supplier_data['company_number'],
      'postal_code' => $supplier_data['company_postal_code'],
      'city'        => $supplier_data['company_city']
    ]);

    $supplier = Supplier::create([
      'company_name'        =>  $supplier_data['company_name'],
      'company_cuit'        =>  $supplier_data['company_cuit'],
      'iva_condition'       =>  $supplier_data['company_iva'],
      'phone_number'        =>  $supplier_data['company_phone'],
      'short_description'   =>  $supplier_data['company_short_desc'],
      'status_is_active'    =>  $supplier_data['company_status_is_active'],
      'status_description'  =>  $supplier_data['company_status_description'],
      'status_date'         =>  $supplier_data['company_status_date'],
      'user_id'             =>  $supplier_user->id,
      'address_id'          =>  $supplier_address->id
    ]);

    return $supplier;
  }

  /**
   * servicio: actualizar proveedor
   * NOTA: recibe un usuario actualizado previamente
   * NOTA: recibe el array de inputs validados
   * @param User $supplier_user
   * @param Supplier $supplier
   * @param Array $supplier_data
   * @return Supplier $supplier actualizado
   */
  public function editSupplier(User $supplier_user, Supplier $supplier, array $supplier_data): Supplier
  {
    // direccion
    $address = $supplier->address;

    $address->street      = $supplier_data['company_street'];
    $address->number      = $supplier_data['company_number'];
    $address->postal_code = $supplier_data['company_postal_code'];
    $address->city        = $supplier_data['company_city'];
    $address->save();

    // proveedor
    $supplier->company_name        = $supplier_data['company_name'];
    $supplier->company_cuit        = $supplier_data['company_cuit'];
    $supplier->iva_condition       = $supplier_data['company_iva'];
    $supplier->phone_number        = $supplier_data['company_phone'];
    $supplier->short_description   = $supplier_data['company_short_desc'];
    $supplier->status_is_active    = $supplier_data['status_is_active'];
    $supplier->status_description  = $supplier_data['status_description'];
    $supplier->status_date         = $supplier_data['status_date'];
    $supplier->user_id             = $supplier_user->id;
    $supplier->address_id          = $address->id;
    $supplier->save();

    return $supplier;
  }


  /**
   * Eliminar proveedor
   * NOTA: elimina el usuario y dirección asociados
   * @param Supplier $supplier
   * @return void
   */
  public function deleteSupplier(Supplier $supplier): void
  {
    DB::transaction(function () use ($supplier) {
      // 1. primero eliminar supplier (tiene las FK)
      $supplier->status_is_active = false;
      $supplier->status_description = 'Proveedor inactivo';
      $supplier->save();
      $supplier->delete();

      // 2. luego eliminar las entidades referenciadas
      $supplier->user?->delete();
      $supplier->address?->delete();
    });
  }

  /**
   * Restaurar proveedor
   * NOTA: restaura el usuario y dirección asociados
   * @param Supplier $supplier_id
   * @return void
   */
  public function restoreSupplier(int $supplier_id): void
  {
    $supplier = Supplier::withTrashed()->findOrFail($supplier_id);

    if (!$supplier->trashed()) {
      throw new InvalidArgumentException('El proveedor no está eliminado');
    }

    DB::transaction(function () use ($supplier) {
      // restaurar dependencias usando los IDs directamente
      User::withTrashed()->where('id', $supplier->user_id)->restore();
      Address::withTrashed()->where('id', $supplier->address_id)->restore();

      $supplier->restore();
      $supplier = $supplier->refresh();
      $supplier->status_is_active = true;
      $supplier->status_description = 'Proveedor activo';
      $supplier->save();
    });
  }

  /**
   * servicio: obtener marcas de suministros
   * @param string $order_by orden de los registros
   * @return Collection $provision_trademarks
   */
  public function getProvisionTrademarks($order_by = 'asc'): Collection
  {
    return ProvisionTrademark::orderBy('provision_trademark_name', $order_by)->get();
  }

  /**
   * servicio: obtener tipos de suministros
   * @return Collection $provision_types
   */
  public function getProvisionTypes(): Collection
  {
    return ProvisionType::all();
  }
}
