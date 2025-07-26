<?php

namespace App\Services\Supplier;

use App\Models\RequestForQuotationPeriod;
use App\Models\PackPeriod;
use App\Models\PeriodProvision;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * * Sincronizar correctamente suministros y packs del periodo presupuestario
 */
class PeriodSyncService
{
    /**
     * Sincroniza todas las relaciones del período desde la colección de Livewire
     */
    public function syncPeriodRelations(RequestForQuotationPeriod $period, Collection $provisionsAndPacks): void
    {
        DB::transaction(function () use ($period, $provisionsAndPacks) {
            // Separar packs y suministros
            $packsData = $this->extractPacksData($provisionsAndPacks);
            $provisionsData = $this->extractProvisionsData($provisionsAndPacks);

            // Sincronizar cada tipo de relación
            $this->syncPacks($period, $packsData);
            $this->syncProvisions($period, $provisionsData);
        });
    }

    /**
     * Extrae datos de packs de la colección
     */
    private function extractPacksData(Collection $provisionsAndPacks): array
    {
        return $provisionsAndPacks
            ->where('item_type', 'pack')
            ->map(function ($item) {
                return [
                    'pack_id' => $item['item_object']->id,
                    'quantity' => (int) $item['item_quantity']
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Extrae datos de suministros de la colección
     */
    private function extractProvisionsData(Collection $provisionsAndPacks): array
    {
        return $provisionsAndPacks
            ->where('item_type', 'suministro')
            ->map(function ($item) {
                return [
                    'provision_id' => $item['item_object']->id,
                    'quantity' => (int) $item['item_quantity']
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Sincroniza las relaciones de packs del período
     */
    private function syncPacks(RequestForQuotationPeriod $period, array $packsData): void
    {
        $this->syncPivotRelation(
            $period,
            $packsData,
            PackPeriod::class,
            'pack_id',
            'period_id'
        );
    }

    /**
     * Sincroniza las relaciones de suministros del período
     */
    private function syncProvisions(RequestForQuotationPeriod $period, array $provisionsData): void
    {
        $this->syncPivotRelation(
            $period,
            $provisionsData,
            PeriodProvision::class,
            'provision_id',
            'period_id'
        );
    }

    /**
     * Método genérico para sincronizar relaciones pivot con auditoría
     */
    private function syncPivotRelation(
        RequestForQuotationPeriod $period,
        array $newData,
        string $pivotModel,
        string $foreignKey,
        string $periodKey
    ): void {
        // Si no hay datos nuevos, eliminar todos los existentes
        if (empty($newData)) {
            $pivotModel::where($periodKey, $period->id)->each(function ($record) {
                $record->delete(); // Mantiene auditoría
            });
            return;
        }

        // Obtener registros existentes
        $existing = $pivotModel::where($periodKey, $period->id)->get();

        // Crear mapas para comparación eficiente
        $existingMap = $existing->keyBy($foreignKey);

        // Crear mapa de nuevos datos manteniendo la estructura de array
        $newDataMap = collect($newData)->mapWithKeys(function ($item) use ($foreignKey) {
            return [$item[$foreignKey] => $item];
        });

        // Identificar registros a eliminar (existen pero no están en los nuevos datos)
        $toDelete = $existingMap->diffKeys($newDataMap);

        // Identificar registros a actualizar (existen en ambos)
        $toUpdate = $existingMap->intersectByKeys($newDataMap);

        // Identificar registros a crear (están en nuevos datos pero no existen)
        $toCreate = $newDataMap->diffKeys($existingMap);

        // Eliminar registros obsoletos
        $this->deleteObsoleteRecords($toDelete);

        // Actualizar registros existentes
        $this->updateExistingRecords($toUpdate, $newDataMap);

        // Crear nuevos registros
        $this->createNewRecords($toCreate, $pivotModel, $period->id, $periodKey);
    }

    /**
     * Elimina registros obsoletos individualmente para mantener auditoría
     */
    private function deleteObsoleteRecords(Collection $toDelete): void
    {
        foreach ($toDelete as $record) {
            $record->delete(); // Dispara eventos de auditoría
        }
    }

    /**
     * Actualiza registros existentes individualmente para mantener auditoría
     */
    private function updateExistingRecords(Collection $toUpdate, Collection $newDataMap): void
    {
        foreach ($toUpdate as $foreignId => $existingRecord) {
            $newData = $newDataMap->get($foreignId);

            // Solo actualizar si hay cambios
            if (is_array($newData) && $this->hasChanges($existingRecord, $newData)) {
                $existingRecord->fill($newData);
                $existingRecord->save(); // Dispara eventos de auditoría
            }
        }
    }

    /**
     * Crea nuevos registros individualmente para mantener auditoría
     */
    private function createNewRecords(
        Collection $toCreate,
        string $pivotModel,
        int $periodId,
        string $periodKey
    ): void {
        foreach ($toCreate as $foreignId => $data) {
            if (is_array($data)) {
                $pivotModel::create([
                    $periodKey => $periodId,
                    ...$data // Spread operator para incluir todos los datos
                ]); // Dispara eventos de auditoría
            }
        }
    }

    /**
     * Verifica si hay cambios en los datos del registro
     */
    private function hasChanges($existingRecord, array $newData): bool
    {
        foreach ($newData as $key => $value) {
            // Ignorar las claves que no son campos actualizables
            if (in_array($key, ['period_id', 'pack_id', 'provision_id'])) {
                continue;
            }

            // Comparar valores considerando tipos
            if ($existingRecord->{$key} != $value) {
                return true;
            }
        }
        return false;
    }
}
