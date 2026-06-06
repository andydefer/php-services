<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\DomainStructures\Abstracts\AbstractData;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\PhpServices\Contracts\ModelTransformableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class ModelTransformableService implements ModelTransformableInterface
{
    public function toData(Model $model, string $dataClass): AbstractData
    {
        $attributes = $this->extractAttributes($model);
        $relations = $this->extractRelations($model);

        $data = array_merge($attributes, $relations);

        return $dataClass::from($data);
    }

    public function toDataCollection(Collection $models, string $dataClass): array
    {
        return $models->map(
            fn (Model $model) => $this->toData($model, $dataClass)
        )->values()->toArray();
    }

    private function extractAttributes(Model $model): array
    {
        $attributes = [];

        foreach ($model->getAttributes() as $key => $value) {
            $attributes[$key] = $this->transformValue($model, $key, $value);
        }

        return $attributes;
    }

    private function extractRelations(Model $model): array
    {
        $relations = [];

        foreach ($model->getRelations() as $relationName => $relationValue) {
            $relations[$relationName] = $this->transformRelation($relationValue);
        }

        return $relations;
    }

    private function transformValue(Model $model, string $key, mixed $value): mixed
    {
        // DEBUG: Afficher le type de valeur pour les clés 'metadata'
        if ($key === 'metadata') {
        }

        // Date / DateTime
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d\TH:i:sP');
        }

        // JSON / Array - décoder d'abord
        if ($model->hasCast($key, 'json') || $model->hasCast($key, 'array')) {
            if ($value === null) {
                return null;
            }

            // DEBUG: Afficher avant décodage
            if ($key === 'metadata') {
            }

            $decoded = json_decode($value, true);

            // DEBUG: Afficher après décodage
            if ($key === 'metadata') {
            }

            // Si le décodage a échoué, retourner un tableau vide
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                if ($key === 'metadata') {
                }

                return StrictDataObject::from([]);
            }

            return StrictDataObject::from($decoded);
        }

        // Enum (PHP 8.1+)
        if ($model->hasCast($key, 'enum')) {
            return $value?->value ?? $value;
        }

        return $value;
    }

    private function transformRelation(mixed $relationValue): mixed
    {
        if ($relationValue instanceof Model) {
            $dataClass = $this->guessDataClass($relationValue);

            return $this->toData($relationValue, $dataClass);
        }

        if ($relationValue instanceof Collection) {
            return $relationValue->map(
                fn (Model $item) => $this->toData($item, $this->guessDataClass($item))
            )->values()->toArray();
        }

        return $relationValue;
    }

    private function guessDataClass(Model $model): string
    {
        $modelClass = get_class($model);
        $modelName = class_basename($modelClass);

        // App\Models\User -> App\Data\UserData
        $dataClass = str_replace('Models', 'Data', $modelClass).'Data';

        if (class_exists($dataClass)) {
            return $dataClass;
        }

        // Fallback: App\Data\{ModelName}Data
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
