<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts;

use AndyDefer\DomainStructures\Abstracts\AbstractData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ModelTransformableInterface
{
    /**
     * Convertit un modèle Eloquent en Data DTO.
     *
     * @template TData of AbstractData
     *
     * @param  class-string<TData>  $dataClass
     * @return TData
     */
    public function toData(Model $model, string $dataClass): AbstractData;

    /**
     * Convertit une collection de modèles en collection de Data DTO.
     *
     * @template TData of AbstractData
     *
     * @param  Collection<int, Model>  $models
     * @param  class-string<TData>  $dataClass
     * @return array<int, TData>
     */
    public function toDataCollection(Collection $models, string $dataClass): array;
}
