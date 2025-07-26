<?php

namespace App\Traits;

use App\Models\User;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait para manejar auditorías manuales en comandos y procesos automáticos
 */
trait ManualAuditTrait
{
    /**
     * Crear registro de auditoría manual
     */
    protected function createManualAudit(
        Model $model,
        string $event,
        ?User $user = null,
        array $old_values = [],
        array $new_values = [],
        array $additional_info = []
    ): ?Audit {
        
        try {
            $audit_data = [
                'user_type' => $user ? get_class($user) : null,
                'user_id' => $user?->id,
                'event' => $event,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->id,
                'old_values' => !empty($old_values) ? $old_values : [],
                'new_values' => !empty($new_values) ? $new_values : [],
                'url' => $this->getCurrentUrl(),
                'ip_address' => $this->getCurrentIpAddress(),
                'user_agent' => $this->getCurrentUserAgent(),
                'tags' => $this->getAuditTags($additional_info),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Log para debugging
            Log::info('Creating manual audit', [
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'event' => $event,
                'user_id' => $user?->id,
                'audit_data' => $audit_data
            ]);
            
            $audit = Audit::create($audit_data);
            
            Log::info('Manual audit created successfully', [
                'audit_id' => $audit->id
            ]);
            
            return $audit;
            
        } catch (\Exception $e) {
            Log::error('Error creating manual audit', [
                'error' => $e->getMessage(),
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }
    
    /**
     * Crear auditoría para modelo creado
     */
    public function auditModelCreated(
        Model $model,
        ?User $user = null,
        array $additional_info = []
    ): ?Audit {
        return $this->createManualAudit(
            model: $model,
            event: 'created',
            user: $user,
            old_values: [],
            new_values: $model->getAttributes(),
            additional_info: $additional_info
        );
    }
    
    /**
     * Crear auditoría para modelo actualizado
     */
    public function auditModelUpdated(
        Model $model,
        array $original_attributes,
        ?User $user = null,
        array $additional_info = []
    ): ?Audit {
        $changed_attributes = [];
        $old_values = [];
        
        foreach ($model->getChanges() as $key => $new_value) {
            $changed_attributes[$key] = $new_value;
            $old_values[$key] = $original_attributes[$key] ?? null;
        }
        
        return $this->createManualAudit(
            model: $model,
            event: 'updated',
            user: $user,
            old_values: $old_values,
            new_values: $changed_attributes,
            additional_info: $additional_info
        );
    }
    
    /**
     * Crear auditoría para modelo eliminado
     */
    public function auditModelDeleted(
        Model $model,
        ?User $user = null,
        array $additional_info = []
    ): ?Audit {
        return $this->createManualAudit(
            model: $model,
            event: 'deleted',
            user: $user,
            old_values: $model->getAttributes(),
            new_values: [],
            additional_info: $additional_info
        );
    }
    
    /**
     * Obtener URL actual (para comandos será null)
     */
    private function getCurrentUrl(): ?string
    {
        if (app()->runningInConsole()) {
            return null;
        }
        
        try {
            return request()->fullUrl();
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Obtener IP actual
     */
    private function getCurrentIpAddress(): string
    {
        if (app()->runningInConsole()) {
            return '127.0.0.1';
        }
        
        try {
            return request()->ip() ?? '127.0.0.1';
        } catch (\Exception $e) {
            return '127.0.0.1';
        }
    }
    
    /**
     * Obtener User Agent actual
     */
    private function getCurrentUserAgent(): string
    {
        if (app()->runningInConsole()) {
            $command = $_SERVER['argv'][1] ?? 'unknown-command';
            return "Laravel Command: {$command}";
        }
        
        try {
            return request()->userAgent() ?? 'Laravel Queue Job';
        } catch (\Exception $e) {
            return 'Laravel Queue Job';
        }
    }
    
    /**
     * Generar tags para la auditoría
     */
    private function getAuditTags(array $additional_info = []): string
    {
        $tags = [];
        
        if (app()->runningInConsole()) {
            $tags[] = 'command';
            $tags[] = 'automatic';
        } else {
            $tags[] = 'web';
            $tags[] = 'manual';
        }
        
        // Agregar tags adicionales basados en la información
        if (isset($additional_info['reason'])) {
            $tags[] = $additional_info['reason'];
        }
        
        if (isset($additional_info['job'])) {
            $tags[] = str_replace('-', '_', $additional_info['job']);
        }
        
        if (isset($additional_info['command'])) {
            $tags[] = str_replace(':', '-', $additional_info['command']);
        }
        
        return implode(',', $tags);
    }
}