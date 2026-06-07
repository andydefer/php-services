<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Abstracts\AbstractTypedCollection;
use AndyDefer\DomainStructures\Normalizers\NormalizerChain;
use AndyDefer\PhpServices\Contracts\RecordTransformableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class RecordTransformableService implements RecordTransformableInterface
{
    public function toRecord(Model $model, string $recordClass): AbstractRecord
    {
        $attributes = $this->extractAttributes($model);
        $relations = $this->extractRelations($model);
        $data = array_merge($attributes, $relations);
        $normalized = NormalizerChain::get()->normalize($data);

        return $recordClass::from($normalized);
    }

    public function toRecordCollection(
        Collection $models,
        string $collectionClass
    ): AbstractTypedCollection {
        $data = [];

        foreach ($models as $model) {
            $attributes = $this->extractAttributes($model);
            $relations = $this->extractRelations($model);
            $item = array_merge($attributes, $relations);
            $data[] = NormalizerChain::get()->normalize($item);
        }

        return $collectionClass::collect($data);
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

    private function transformRelation(mixed $relationValue): mixed
    {
        if ($relationValue instanceof Collection) {
            return $relationValue->values()->toArray();
        }

        if ($relationValue instanceof Model) {
            return $this->toRecord($relationValue, $this->guessRecordClass($relationValue));
        }

        return $relationValue;
    }

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
