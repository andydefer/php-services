<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\DomainStructures\Abstracts\AbstractData;
use AndyDefer\DomainStructures\Abstracts\AbstractTypedCollection;
use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\PhpServices\Contracts\ModelTransformableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @deprecated Ce service est déprécié. Utilisez HydrationService du package domain-structures à la place.
 *
 * Ce service sera supprimé dans la version 1.0.0.
 *
 * ❌ À NE PLUS UTILISER :
 * - ModelTransformableService::toData()
 * - ModelTransformableService::toDataCollection()
 *
 * ✅ RECOMMANDATION :
 * Utilisez HydrationService pour toutes les opérations d'hydratation :
 *
 * // Pour un seul modèle :
 * $hydrationService = new HydrationService();
 * $data = $hydrationService->hydrate(MyData::class, $model->toArray());
 *
 * // Pour une collection :
 * $collection = $hydrationService->collect($models, MyCollection::class);
 *
 * // Depuis du JSON :
 * $data = $hydrationService->hydrateFromJson(MyData::class, $jsonString);
 * $collection = $hydrationService->collectFromJson($jsonString, MyCollection::class);
 * @see HydrationService
 * @deprecated
 */
final class ModelTransformableService implements ModelTransformableInterface
{
    private HydrationService $hydrationService;

    public function __construct()
    {
        // @trigger_error(sprintf(
        //     'L\'utilisation de %s est dépréciée. Utilisez %s à la place.',
        //     __CLASS__,
        //     HydrationService::class
        // ), E_USER_DEPRECATED);

        $this->hydrationService = new HydrationService;
    }

    /**
     * @deprecated Utilisez HydrationService::hydrate() à la place
     *
     * Anciennement : Convertit un modèle Eloquent en Data object
     *
     * ✅ NOUVELLE APPROCHE :
     * $data = $hydrationService->hydrate(MyData::class, $model->toArray());
     */
    public function toData(Model $model, string $dataClass): AbstractData
    {
        @trigger_error(
            sprintf(
                '%s::toData() est dépréciée. Utilisez %s::hydrate() avec $model->toArray() à la place.',
                __CLASS__,
                HydrationService::class
            ),
            E_USER_DEPRECATED
        );

        $attributes = $this->extractAttributes($model);
        $relations = $this->extractRelations($model);
        $data = array_merge($attributes, $relations);

        // Recommandation d'utilisation du nouveau service
        return $this->hydrationService->hydrate($dataClass, $data);
    }

    /**
     * @deprecated Utilisez HydrationService::collect() à la place
     *
     * Anciennement : Convertit une collection de modèles en TypedCollection
     *
     * ✅ NOUVELLE APPROCHE :
     * $collection = $hydrationService->collect($models->toArray(), MyCollection::class);
     */
    public function toDataCollection(
        Collection $models,
        string $collectionClass
    ): AbstractTypedCollection {
        @trigger_error(
            sprintf(
                '%s::toDataCollection() est dépréciée. Utilisez %s::collect() avec $models->toArray() à la place.',
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
     */
    private function transformRelation(mixed $relationValue): mixed
    {
        if ($relationValue instanceof Collection) {
            return $relationValue->values()->toArray();
        }

        if ($relationValue instanceof Model) {
            return $this->toData($relationValue, $this->guessDataClass($relationValue));
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

            $decoded = json_decode($value, true);

            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                return StrictDataObject::from([]);
            }

            return StrictDataObject::from($decoded);
        }

        if ($model->hasCast($key, 'enum')) {
            return $value?->value ?? $value;
        }

        return $value;
    }

    /**
     * @deprecated Cette méthode privée sera supprimée
     */
    private function guessDataClass(Model $model): string
    {
        $modelClass = get_class($model);
        $modelName = class_basename($modelClass);

        $dataClass = str_replace('Models', 'Data', $modelClass).'Data';

        if (class_exists($dataClass)) {
            return $dataClass;
        }

        $fallbackClass = 'App\\Data\\'.$modelName.'Data';

        if (class_exists($fallbackClass)) {
            return $fallbackClass;
        }

        throw new \RuntimeException(sprintf(
            'Data class not found for model %s. Tried: %s and %s',
            $modelClass,
            $dataClass,
            $fallbackClass
        ));
    }
}
