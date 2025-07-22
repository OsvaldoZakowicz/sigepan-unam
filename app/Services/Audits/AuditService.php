<?php

namespace App\Services\Audits;

class AuditService
{
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
                'name' => 'Nombre',
                'email' => 'Correo Electrónico',
                'email_verified_at' => 'Email Verificado',
                'password' => 'Contraseña',
                'remember_token' => 'Token de Recordatorio',
                'created_at' => 'Fecha de Creación',
                'updated_at' => 'Fecha de Actualización',
            ]
        ],
    ];

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