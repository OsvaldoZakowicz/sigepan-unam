<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;

class RoleSeeder extends Seeder
{
  public function run(): void
  {
    //* roles internos
    $tableAttributesInternRoles = [
      'guard_name' => 'web',
      'is_editable' => false,
      'is_internal' => true
    ];

    $gerente = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'gerente'));
    $panadero = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'panadero'));
    $vendedor = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'vendedor'));
    $repartidor = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'repartidor'));
    $proveedor = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'proveedor'));
    $administrador = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'administrador'));
    $auditor = Role::create(Arr::add($tableAttributesInternRoles, 'name', 'auditor'));

    // permisos
    $gerente->syncPermissions([
      'panel','usuarios','stock','proveedores','ventas','compras','tienda'
    ]);

    $panadero->syncPermissions([
      'panel','stock'
    ]);

    $vendedor->syncPermissions([
      'panel','ventas'
    ]);

    $repartidor->syncPermissions([
      'panel','ventas'
    ]);

    $proveedor->syncPermissions([]);

    $administrador->syncPermissions([
      'panel','usuarios','auditoria','stock','proveedores','ventas','compras','tienda'
    ]);

    $auditor->syncPermissions([
      'panel','auditoria'
    ]);

    //* roles no internos (is_internal = false)
    $tableAttributesRoles = [
      'guard_name' => 'web',
      'is_editable' => false,
      'is_internal' => false
    ];

    $cliente = Role::create(Arr::add($tableAttributesRoles, 'name', 'cliente'));

    //* permisos estrictamente no internos (is_internal = false)
    $cliente->syncPermissions([
      'tienda'
    ]);
  }
}
