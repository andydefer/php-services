<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Abstracts\AbstractTypedCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RecordTransformableInterface
{
    /**
     * Convertit un modèle Eloquent en Record.
     *
     * @template TRecord of AbstractRecord
     *
     * @param  class-string<TRecord>  $recordClass
     * @return TRecord
     */
    public function toRecord(Model $model, string $recordClass): AbstractRecord;

    /**
     * Convertit une collection de modèles en collection typée.
     *
     * @template TCollection of AbstractTypedCollection
     *
     * @param  Collection<int, Model>  $models
     * @param  class-string<TCollection>  $collectionClass
     * @return TCollection
     */
    public function toRecordCollection(
        Collection $models,
        string $collectionClass
    ): AbstractTypedCollection;
}
