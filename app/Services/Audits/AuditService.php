<?php

namespace App\Services\Audits;

use App\Models\User;
use App\Traits\ManualAuditTrait;

class AuditService
{

    // especificamente cuando necesito crear registros de auditoria manualmente
    use ManualAuditTrait;

    /**
     * tipos de eventos auditados con sus traducciones
     */
    public const AUDIT_EVENTS = [
        'created' => 'Creado',
        'updated' => 'Actualizado',
        'deleted' => 'Eliminado',
        'restored' => 'Restaurado',
    ];

    /**
     * mapeo de modelos auditables con sus traducciones
     * * todos los modelos y atributos auditados van mapeados aqui
     */
    public const AUDITABLE_MODELS = [
        'App\Models\User' => [
            'table' => 'usuarios',
            'model' => 'Usuario',
            'attributes' => [
                'id'=> 'id',
                'name' => 'Nombre',
                'email' => 'Correo Electrónico',
                'email_verified_at' => 'Email Verificado',
                'password' => 'Contraseña',
                'remember_token' => 'Token de Recordatorio',
                'is_first_login' => 'Inició sesión por primera vez',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\Address' => [
            'table' => 'direcciones',
            'model' => 'Direccion',
            'attributes' => [
                'id'=> 'id',
                'street' => 'calle',
                'number' => 'numero de calle',
                'postal_code' => 'codigo postal',
                'city' => 'ciudad',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        // modulo de proveedores
        'App\Models\Supplier' => [
            'table' => 'proveedores',
            'model' => 'Proveedor',
            'attributes' => [
                'id' => 'id',
                'company_name' => 'razon social',
                'company_cuit' => 'cuit',
                'iva_condition' => 'condicion frente al iva',
                'phone_number' => 'telefono de contacto',
                'short_description' => 'descripcion corta',
                'status_is_active' => 'estado',
                'status_description' => 'descripcion del estado',
                'status_date' => 'fecha del estado registrado',
                'user_id' => 'id de usuario',
                'address_id' => 'id de direccion',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\ProvisionTrademark' => [
            'table' => 'marcas',
            'model' => 'Marca',
            'attributes' => [
                'id' => 'id',
                'provision_trademark_name' => 'Nombre',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\ProvisionCategory' => [
            'table' => 'categorias',
            'model' => 'Categoria',
            'attributes' => [
                'id' => 'id',
                'provision_category_name' => 'Nombre',
                'measure_id' => 'Medida de la categoria',
                'provision_type_id' => 'Tipo de la categoria',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\Provision' => [
            'table' => 'suministros',
            'model' => 'Suministro',
            'attributes' => [
                'id' => 'id',
                'provision_name' => 'Nombre del suministro',
                'provision_quantity' => 'Volumen',
                'provision_short_description' => 'Descripcion',
                'provision_trademark_id' => 'Marca',
                'provision_category_id' => 'Categoria',
                'provision_type_id' => 'Tipo',
                'measure_id' => 'Unidad de medida',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\ProvisionSupplier' => [
            'table' => 'precio suministro',
            'model' => 'Suministro Proveedor',
            'attributes' => [
                'id' => 'id',
                'supplier_id' => 'id de proveedor',
                'provision_id' => 'id de suministro',
                'price' => 'precio',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\Pack' => [
            'table' => 'packs',
            'model' => 'Pack',
            'attributes' => [
                'id' => 'id',
                'pack_name' => 'nombre del pack',
                'pack_units' => 'unidades',
                'pack_quantity' => 'volumen',
                'provision_id' => 'id de suministro',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\PackSupplier' => [
            'table' => 'precio pack',
            'model' => 'Pack Proveedor',
            'attributes' => [
                'id' => 'id',
                'supplier_id' => 'id de proveedor',
                'pack_id' => 'id de pack',
                'price' => 'precio',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\RequestForQuotationPeriod' => [
            'table' => 'periodos presupuestarios',
            'model' => 'Periodo Presupuestario',
            'attributes' => [
                'id' => 'id',
                'period_code' => 'codigo de periodo',
                'period_start_at' => 'fecha de inicio',
                'period_end_at' => 'fecha de fin',
                'period_short_description' => 'descripcion corta',
                'period_status_id' => 'id de estado',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\PeriodProvision' => [
            'table' => 'suministros de periodo presupuestario',
            'model' => 'Suministro Periodo Presupuesto',
            'attributes' => [
                'id' => 'id',
                'period_id' => 'id de periodo presupuestario',
                'provision_id' => 'id de suministro',
                'quantity' => 'cantidad solicitada',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\PackPeriod' => [
            'table' => 'packs de periodo presupuestario',
            'model' => 'Pack Periodo Presupuesto',
            'attributes' => [
                'id' => 'id',
                'period_id' => 'id de presupuesto',
                'pack_id' => 'id de pack',
                'quantity' => 'cantidad solicitada',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\Quotation' => [
            'table' => 'presupuestos',
            'model' => 'Presupuesto',
            'attributes' => [
                'id' => 'id',
                'quotation_code' => 'codigo de presupuesto',
                'is_completed' => 'estado de completitud',
                'period_id' => 'id de periodo presupuestario',
                'supplier_id' => 'id de proveedor',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\ProvisionQuotation' => [
            'table' => 'suministros de presupuestos',
            'model' => 'Suministro Presupuesto',
            'attributes' => [
                'id' => 'id',
                'quotation_id' => 'id de presupuesto',
                'provision_id' => 'id de suministro',
                'has_stock' => 'tiene stock',
                'quantity' => 'cantidad solicitada',
                'unit_price' => 'precio unitario',
                'total_price' => 'precio subtotal',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        'App\Models\PackQuotation' => [
            'table' => 'packs de presupuestos',
            'model' => 'Pack Presupuesto',
            'attributes' => [
                'id' => 'id',
                'quotation_id' => 'id de presupuesto',
                'pack_id' => 'id de pack',
                'has_stock' => 'tiene stock',
                'quantity' => 'cantidad solicitada',
                'unit_price' => 'precio unitario',
                'total_price' => 'precio subtotal',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
        // modulo de stock
        'App\Models\Product' => [
            'table' => 'productos',
            'model' => 'Producto',
            'attributes' => [
                'id' => 'id',
                'product_name' => 'nombre de producto',
                'product_short_description' => 'descripcion corta del producto',
                'product_expires_in' => 'vencimiento despues de elaborarse',
                'product_in_store' => 'publicar este producto en la tienda',
                'product_image_path' => 'imagen del producto',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\Price' => [
            'table' => 'precios',
            'model' => 'Precio',
            'attributes' => [
                'id' => 'id',
                'product_id' => 'id de producto',
                'quantity' => 'cantidad del producto',
                'price' => 'precio',
                'description' => 'descripcion del precio',
                'is_default' => 'precio destacado',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\Recipe' => [
            'table' => 'recetas',
            'model' => 'Receta',
            'attributes' => [
                'id' => 'id',
                'recipe_title' => 'titulo de receta',
                'recipe_yields' => 'rendimiento de receta',
                'recipe_portions' => 'porciones de receta',
                'recipe_preparation_time' => 'tiempo de preparacion',
                'recipe_instructions' => 'instrucciones de preparacion',
                'product_id' => 'id de producto',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\Tag' => [
            'table' => 'etiquetas',
            'model' => 'Etiqueta',
            'attributes' => [
                'id' => 'id',
                'tag_name' => 'nombre',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\Stock' => [
            'table' => 'stocks',
            'model' => 'Stock',
            'attributes' => [
                'id'            => 'id',
                'product_id'    => 'id de producto',
                'recipe_id'     => 'id de receta',
                'lote_code'     => 'codigo de lote',
                'quantity_total' => 'cantidad total',
                'quantity_left'  => 'cantidad restante',
                'expired_at'     => 'fecha de vencimiento',
                'elaborated_at'  => 'fecha de elaboracion',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\StockMovement' => [
            'table' => 'movimientos',
            'model' => 'StockMovement',
            'attributes' => [
                'id'            => 'id',
                'stock_id'      => 'id de stock',
                'quantity'      => 'cantidad movida',
                'movement_type' => 'tipo de movimiento',
                'registered_at' => 'fecha de registro',
                'movement_reference_id'   => 'id de referencia del movimiento',
                'movement_reference_type' => 'modelo de referencia del movimiento',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
        'App\Models\Existence' => [
            'table' => 'existencias',
            'model' => 'Existencia',
            'attributes' => [
                'id'            => 'id',
                'provision_id'  => 'id de suministro',
                'purchase_id'   => 'id de compra',
                'stock_id'      => 'id de stock',
                'movement_type' => 'tipo de movimiento',
                'registered_at' => 'fecha de registro',
                'quantity_amount' => 'cantidad',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
                'deleted_at' => 'Fecha de Eliminación'
            ]
        ],
    ];

    /**
     * a partir de un registro de auditoria obtener usuario responsable
     * junto a su rol. Incluso si el usuario esta borrado.
     * 
     * @param int $user_id id del usuario desde los metadatos de auditoria
     * @return array <string, string> usuario responsable, email, rol.
     */
    public function getResponsibleUser(int $user_id): array
    {
        $user = User::withTrashed()->findOrFail($user_id);
        $role = $user->getRolenames()->first();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role,
        ];
    }

    /**
     * obtiene todos los tipos de eventos
     * 
     * @return array <string,string>
     */
    public static function getAuditEvents(): array
    {
        return self::AUDIT_EVENTS;
    }

    /**
     * obtiene solo las claves de eventos (para filtros)
     * 
     * @return array<string>
     */
    public static function getAuditEventKeys(): array
    {
        return array_keys(self::AUDIT_EVENTS);
    }

    /**
     * obtiene la traducción de un evento específico
     * 
     * @param string $eventKey
     * @return string|null
     */
    public static function getEventTranslation(string $eventKey): ?string
    {
        return self::AUDIT_EVENTS[$eventKey] ?? null;
    }

    /**
     * verifica si un evento es válido
     * 
     * @param string $eventKey
     * @return bool
     */
    public static function isValidEvent(string $eventKey): bool
    {
        return array_key_exists($eventKey, self::AUDIT_EVENTS);
    }

    /**
     * obtiene toda la informacion de modelos auditados
     * 
     * @return array
     */
    public static function getAuditableModels(): array
    {
        return self::AUDITABLE_MODELS;
    }

    /**
     * obtiene la informacion completa de un modelo auditable
     * ejemplo: $userInfo = AuditService::getModelInfo('App\Models\User');
     * ['table' => 'usuarios', 'model' => 'Usuario', 'attributes' => [...]]
     * 
     * @param string $modelClass
     * @return array|null
     */
    public static function getModelInfo($modelClass): ?array
    {
        return self::AUDITABLE_MODELS[$modelClass] ?? null;
    }

    /**
     * obtiene el nombre de la tabla traducido
     * ejemplo: $tableName = AuditService::getTableName('App\Models\User'); // 'usuarios'
     * 
     * @param string $modelClass
     * @return string|null
     */
    public static function getTableName(string $modelClass): ?string
    {
        return self::AUDITABLE_MODELS[$modelClass]['table'] ?? null;
    }

    /**
     * obtiene el nombre del modelo traducido
     * ejemplo: $modelName = AuditService::getModelName('App\Models\User'); // 'Usuario'
     * 
     * @param string $modelClass
     * @return string|null
     */
    public static function getModelName(string $modelClass): ?string
    {
        return self::AUDITABLE_MODELS[$modelClass]['model'] ?? null;
    }

    /**
     * obtiene la traducción de un atributo específico
     * ejemplo: $fieldName = AuditService::getAttributeTranslation('App\Models\User', 'email'); //Correo Electrónico
     * 
     * @param string $modelClass
     * @param string $attribute
     * @return string|null
     */
    public static function getAttributeTranslation(string $modelClass, string $attribute): ?string
    {
        return self::AUDITABLE_MODELS[$modelClass]['attributes'][$attribute] ?? null;
    }

    /**
     * obtiene todas las traducciones de atributos para un modelo
     * ejemplo: $attributes = AuditService::getModelAttributes('App\Models\Product'); // [ atributo => traduccion, ...]
     * 
     * @param string $modelClass
     * @return array
     */
    public static function getModelAttributes(string $modelClass): array
    {
        return self::AUDITABLE_MODELS[$modelClass]['attributes'] ?? [];
    }

    /**
     * verifica si un modelo es auditable
     * es decir, si esta en la lista de modelos auditados
     * 
     * @param string $modelClass
     * @return bool
     */
    public static function isAuditableModel(string $modelClass): bool
    {
        return array_key_exists($modelClass, self::AUDITABLE_MODELS);
    }
}