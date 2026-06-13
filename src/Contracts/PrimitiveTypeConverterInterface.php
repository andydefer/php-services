<?php

namespace AndyDefer\PhpServices\Contracts;

use AndyDefer\PhpServices\Enums\PrimitiveType;
use InvalidArgumentException;

/**
 * Interface pour le service de conversion entre types primitifs PHP.
 */
interface PrimitiveTypeConverterInterface
{
    /**
     * Convertit une valeur d'un type primitif vers un autre.
     *
     * @param  mixed  $value  La valeur à convertir
     * @param  PrimitiveType  $targetType  Le type cible
     * @return mixed La valeur convertie
     */
    public function convert(mixed $value, PrimitiveType $targetType): mixed;

    /**
     * Convertit une valeur en retournant une valeur par défaut si échec.
     *
     * @param  mixed  $value  La valeur à convertir
     * @param  PrimitiveType  $targetType  Le type cible
     * @param  mixed  $default  Valeur par défaut en cas d'échec (par défaut null)
     * @return mixed La valeur convertie ou la valeur par défaut
     */
    public function convertOrDefault(mixed $value, PrimitiveType $targetType, mixed $default = null): mixed;

    /**
     * Détecte le type primitif d'une valeur.
     *
     * @param  mixed  $value  La valeur à analyser
     * @return PrimitiveType Le type détecté
     *
     * @throws InvalidArgumentException Si la valeur n'est pas un type primitif PHP
     */
    public function detectType(mixed $value): PrimitiveType;
}
