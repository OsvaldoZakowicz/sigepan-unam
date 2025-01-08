<?php

namespace App\Services\Supplier;

use App\Models\User;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
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
  public function createSupplier(User $supplier_user, Array $supplier_data): Supplier
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
  public function editSupplier(User $supplier_user, Supplier $supplier, Array $supplier_data): Supplier
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
   * servicio: eliminar provedor
   * NOTA: elimina el usuario y direccion asociados
   * @param Supplier $supplier
   * @return void
   */
  public function deleteSupplier(Supplier $supplier): void
  {
    $supplier_user = $supplier->user;
    $supplier_address = $supplier->address;

    $supplier->delete();

    if ($supplier_user) {
      $supplier_user->delete();
    }

    if ($supplier_address) {
      $supplier_address->delete();
    }

    return;
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
