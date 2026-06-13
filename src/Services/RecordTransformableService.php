<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Abstracts\AbstractTypedCollection;
use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\PhpServices\Contracts\RecordTransformableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @deprecated Ce service est déprécié. Utilisez HydrationService du package domain-structures à la place.
 *
 * Ce service sera supprimé dans la version 1.0.0.
 *
 * ❌ À NE PLUS UTILISER :
 * - RecordTransformableService::toRecord()
 * - RecordTransformableService::toRecordCollection()
 *
 * ✅ RECOMMANDATION :
 * Utilisez HydrationService pour toutes les opérations d'hydratation :
 *
 * // Pour un seul modèle vers un Record :
 * $hydrationService = new HydrationService();
 * $record = $hydrationService->hydrate(MyRecord::class, $model->toArray());
 *
 * // Pour une collection vers une RecordCollection :
 * $collection = $hydrationService->collect($models, MyRecordCollection::class);
 *
 * // Depuis du JSON :
 * $record = $hydrationService->hydrateFromJson(MyRecord::class, $jsonString);
 * $collection = $hydrationService->collectFromJson($jsonString, MyRecordCollection::class);
 *
 * // Si vous utilisez AbstractRecord, assurez-vous que vos classes Record
 * // implémentent correctement la méthode from() ou utilisent l'hydratation native.
 * @see HydrationService
 * @deprecated
 */
final class RecordTransformableService implements RecordTransformableInterface
{
    private HydrationService $hydrationService;

    public function __construct()
    {
        @trigger_error(
            sprintf(
                'L\'utilisation de %s est dépréciée. Utilisez %s à la place.',
                __CLASS__,
                HydrationService::class
            ),
            E_USER_DEPRECATED
        );

        $this->hydrationService = new HydrationService;
    }

    /**
     * @deprecated Utilisez HydrationService::hydrate() à la place
     *
     * Anciennement : Convertit un modèle Eloquent en Record object
     *
     * ✅ NOUVELLE APPROCHE :
     * $record = $hydrationService->hydrate(MyRecord::class, $model->toArray());
     */
    public function toRecord(Model $model, string $recordClass): AbstractRecord
    {
        @trigger_error(
            sprintf(
                '%s::toRecord() est dépréciée. Utilisez %s::hydrate() avec $model->toArray() à la place.',
                __CLASS__,
                HydrationService::class
            ),
            E_USER_DEPRECATED
        );

        $attributes = $this->extractAttributes($model);
        $relations = $this->extractRelations($model);
        $data = array_merge($attributes, $relations);

        // Recommandation d'utilisation du nouveau service
        return $this->hydrationService->hydrate($recordClass, $data);
    }

    /**
     * @deprecated Utilisez HydrationService::collect() à la place
     *
     * Anciennement : Convertit une collection de modèles en TypedCollection de Records
     *
     * ✅ NOUVELLE APPROCHE :
     * $collection = $hydrationService->collect($models->toArray(), MyRecordCollection::class);
     */
    public function toRecordCollection(
        Collection $models,
        string $collectionClass
    ): AbstractTypedCollection {
        @trigger_error(
            sprintf(
                '%s::toRecordCollection() est dépréciée. Utilisez %s::collect() avec $models->toArray() à la place.',
                __CLASS__,
                HydrationService::class
            ),
            E_USER_DEPRECATED
        );

        $data = [];

        foreach ($models as $model) {
            $attributes = $this->extractAttributes($model);
            $relations = $this->extractRelations($model);
            $item = array_merge($attributes, $relations);
            $data[] = $item;
        }

        // Recommandation d'utilisation du nouveau service
        return $this->hydrationService->collect($data, $collectionClass);
    }

    /**
     * @deprecated Cette méthode privée sera supprimée
     * Utilisez directement les méthodes de HydrationService
     */
    private function extractAttributes(Model $model): array
    {
        $attributes = [];

        foreach ($model->getAttributes() as $key => $value) {
            $attributes[$key] = $this->transformValue($model, $key, $value);
        }

        return $attributes;
    }

    /**
     * @deprecated Cette méthode privée sera supprimée
     * Utilisez directement les méthodes de HydrationService
     */
    private function extractRelations(Model $model): array
    {
        $relations = [];

        foreach ($model->getRelations() as $relationName => $relationValue) {
            $relations[$relationName] = $this->transformRelation($relationValue);
        }

        return $relations;
    }

    /**
     * @deprecated Cette méthode privée sera supprimée
     * La gestion des relations sera assurée par HydrationService
     */
    private function transformRelation(mixed $relationValue): mixed
    {
        if ($relationValue instanceof Collection) {
            return $relationValue->values()->toArray();
        }

        if ($relationValue instanceof Model) {
            // Note: Cette récursion peut causer des problèmes de performance
            // HydrationService gère cela plus efficacement
            return $this->toRecord($relationValue, $this->guessRecordClass($relationValue));
        }

        return $relationValue;
    }

    /**
     * @deprecated Cette méthode privée sera supprimée
     * La transformation des valeurs sera gérée par le NormalizerChain d'HydrationService
     */
    private function transformValue(Model $model, string $key, mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d\TH:i:sP');
        }

        if ($model->hasCast($key, 'json') || $model->hasCast($key, 'array')) {
            if ($value === null) {
                return null;
            }

            if (is_array($value)) {
                return $value;
            }

            $decoded = json_decode($value, true);

            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }

            return $decoded;
        }

        if ($model->hasCast($key, 'enum')) {
            return $value?->value ?? $value;
        }

        return $value;
    }

    /**
     * @deprecated Cette méthode privée sera supprimée
     * La convention de nommage des classes Record devrait être gérée par l'application
     */
    private function guessRecordClass(Model $model): string
    {
        $modelClass = get_class($model);
        $modelName = class_basename($modelClass);

        $recordClass = str_replace('Models', 'Records', $modelClass).'Record';

        if (class_exists($recordClass)) {
            return $recordClass;
        }

        $fallbackClass = 'App\\Records\\'.$modelName.'Record';

        if (class_exists($fallbackClass)) {
            return $fallbackClass;
        }

        throw new \RuntimeException(sprintf(
            'Record class not found for model %s. Tried: %s and %s',
            $modelClass,
            $recordClass,
            $fallbackClass
        ));
    }
}
