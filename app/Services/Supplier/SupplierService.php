<?php

namespace App\Services\Supplier;

use App\Models\Supplier;
use App\Models\User;
use App\Models\Address;

class SupplierService
{
  //* rol proveedor
  protected $SUPPLIER_ROLE = 'proveedor';

  //* reglas sobre proveedores
  protected $IVA_CONDITIONS = [
    'responsable inscripto',
    'monotributista',
    'iva excento'
  ];

  /**
   * * servicio: obtener condiciones frente al iva
   */
  public function getSuppilerIvaConditions(): Array
  {
    return $this->IVA_CONDITIONS;
  }

  /**
   * * servicio: obtener rol proveedor
   */
  public function getSupplierRolename(): string
  {
    return $this->SUPPLIER_ROLE;
  }

  /**
   * * servicio: crear proveedor
   * NOTA: recibe un usuario creado previamente
   * NOTA: recibe el array de inputs validados
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
      'company_name'        => $supplier_data['company_name'],
      'company_cuit'        => $supplier_data['company_cuit'],
      'iva_condition'       => $supplier_data['company_iva'],
      'phone_number'        => $supplier_data['company_phone'],
      'short_description'   => $supplier_data['company_short_desc'],
      'user_id'             => $supplier_user->id,
      'address_id'          => $supplier_address->id
    ]);

    return $supplier;
  }

  /**
   * * servicio: eliminar provedor
   * NOTA: elimina el usuario y direccion asociados
   */
  public function deleteSupplier(Supplier $supplier)
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

}
